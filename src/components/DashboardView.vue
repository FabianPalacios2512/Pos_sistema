<template>
  <div class="min-h-screen font-sans bg-[#EEF2F6] text-slate-600 selection:bg-indigo-500 selection:text-white pb-12">
    
    <!-- 🎯 Tour Contextual del Dashboard -->
    <ContextualTour 
      ref="dashboardTourRef"
      module-name="dashboard"
      :steps="tourSteps"
      :auto-start="false"
      @complete="handleTourComplete"
      @skip="handleTourSkip"
    />
    
    <div class="pt-6 px-4 sm:px-6 lg:px-8 space-y-6 animate-fade-in w-full max-w-[1600px] mx-auto">
    
      <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6 bg-white/50 p-4 rounded-[24px] border border-white shadow-sm backdrop-blur-sm">
        <div class="flex items-center space-x-4">
          <div class="w-14 h-14 bg-slate-900 rounded-2xl flex items-center justify-center shadow-lg shadow-slate-300">
            <div class="w-8 h-8 flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m0 0h2M7 3h10M12 13h.01M12 17h.01"></path>
                </svg>
            </div>
          </div>
          <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Panel de Control</h1>
            <p class="text-xs font-bold text-slate-500 flex items-center gap-2 mt-1 uppercase tracking-wide">
              <span class="relative flex h-2.5 w-2.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
              </span>
              <span v-if="loading" class="text-indigo-600">Sincronizando...</span>
              <span v-else>Sistema Operativo • {{ formatDateTime(new Date().toISOString()) }}</span>
            </p>
          </div>
        </div>
        
        <div class="flex items-center gap-3 w-full md:w-auto">
          <button @click="refreshData" 
                  :disabled="loading"
                  class="px-5 py-2.5 bg-white hover:bg-slate-50 text-slate-600 text-sm font-bold rounded-xl border border-slate-200 shadow-sm transition-all duration-200 active:scale-95 flex items-center gap-2 group">
            <svg :class="['w-4 h-4 text-slate-400 group-hover:text-indigo-600 transition-colors', loading ? 'animate-spin' : '']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span>Refrescar</span>
          </button>
          
          <button 
            id="tour-nueva-venta-btn"
            @click="newSale" 
            class="px-6 py-2.5 bg-slate-900 hover:bg-black text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 hover:shadow-slate-400/60 transition-all duration-300 transform active:scale-95 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Nueva Venta</span>
          </button>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <div id="tour-estado-caja" class="bg-white rounded-[24px] p-5 border border-white shadow-[0_2px_20px_-4px_rgba(0,0,0,0.05)] hover:shadow-lg transition-all duration-300 flex items-center justify-between group relative overflow-hidden">
            <div class="absolute right-0 top-0 w-32 h-full bg-gradient-to-l from-emerald-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500 pointer-events-none"></div>
            
            <div class="flex items-center gap-5 relative z-10">
              <div class="w-14 h-14 rounded-2xl flex items-center justify-center transition-all duration-300 border shadow-sm" 
                   :class="hasOpenSession ? 'bg-emerald-50 border-emerald-100 text-emerald-600' : 'bg-slate-50 border-slate-100 text-slate-400'">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path v-if="hasOpenSession" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
              </div>
              <div>
                <h4 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Estado de Caja</h4>
                <div class="flex items-center gap-2">
                    <span class="text-xl font-black tracking-tight" :class="hasOpenSession ? 'text-emerald-600' : 'text-slate-700'">
                      {{ hasOpenSession ? 'Abierta' : 'Cerrada' }}
                    </span>
                    <span v-if="hasOpenSession" class="px-2 py-0.5 bg-slate-100 rounded-md text-[10px] font-bold text-slate-500 border border-slate-200">
                      ID #{{ currentSession?.id }}
                    </span>
                </div>
              </div>
            </div>
            
            <div class="text-right relative z-10">
               <p v-if="hasOpenSession && currentSession" class="text-2xl font-black text-slate-900 tracking-tighter mb-1">
                 ${{ formatCurrency(realCashAvailable) }}
               </p>
               <button @click="hasOpenSession ? handleCloseCash() : handleOpenCash()" 
                       :disabled="cashLoading"
                       class="text-xs font-bold px-4 py-2 rounded-xl transition-all shadow-sm"
                       :class="hasOpenSession ? 'bg-white text-rose-600 border border-rose-100 hover:bg-rose-50' : 'bg-emerald-600 text-white hover:bg-emerald-700 border border-transparent'">
                 {{ cashLoading ? '...' : (hasOpenSession ? 'Cerrar Turno' : 'Abrir Caja') }}
               </button>
            </div>
        </div>

        <div class="bg-white rounded-[24px] p-5 border border-white shadow-[0_2px_20px_-4px_rgba(0,0,0,0.05)] hover:shadow-lg transition-all duration-300 flex items-center justify-between">
            <div class="flex items-center gap-5">
              <div class="w-14 h-14 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center shadow-sm">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
              </div>
              <div>
                <h4 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Equipo Activo</h4>
                 <div class="flex items-center">
                    <div class="flex -space-x-3 mr-4">
                        <div class="w-8 h-8 rounded-full bg-slate-200 border-[3px] border-white"></div>
                        <div class="w-8 h-8 rounded-full bg-slate-300 border-[3px] border-white"></div>
                        <div class="w-8 h-8 rounded-full bg-slate-800 border-[3px] border-white flex items-center justify-center text-[10px] text-white font-bold">+{{activeSessionsCount}}</div>
                    </div>
                    <span class="text-sm font-bold text-slate-700">En línea</span>
                 </div>
              </div>
            </div>
            
            <button @click="goToCashAdmin" class="w-10 h-10 rounded-xl bg-slate-50 hover:bg-indigo-50 text-slate-400 hover:text-indigo-600 transition-all flex items-center justify-center border border-slate-100">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
            </button>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <div class="group bg-white rounded-[24px] p-5 border border-white shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden flex items-center gap-4">
          <div class="absolute -right-6 -bottom-6 opacity-5 group-hover:opacity-10 transition-opacity">
             <div class="w-32 h-32 bg-emerald-500 rounded-full blur-3xl"></div>
          </div>
          
          <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex-shrink-0 flex items-center justify-center text-emerald-600 border border-emerald-100 shadow-inner">
             <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
             </svg>
          </div>

          <div class="relative z-10 flex-1">
             <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Ventas Hoy</p>
             <h3 class="text-2xl font-black text-slate-900 tracking-tighter leading-none mb-1">${{ formatCurrency(salesDataComputed.today.revenue || 0) }}</h3>
             <div class="flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                <span class="text-[10px] font-bold text-slate-500">{{ salesDataComputed.today.sales }} transacciones</span>
             </div>
          </div>
        </div>

        <div class="group bg-white rounded-[24px] p-5 border border-white shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden flex items-center gap-4">
           <div class="w-14 h-14 bg-blue-50 rounded-2xl flex-shrink-0 flex items-center justify-center text-blue-600 border border-blue-100 shadow-inner">
             <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
             </svg>
           </div>
           
           <div class="relative z-10 flex-1">
              <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Salientes</p>
              <h3 class="text-2xl font-black text-slate-900 tracking-tighter leading-none mb-1">{{ salesDataComputed.today.items_sold || 0 }} <span class="text-sm font-bold text-slate-300">ud.</span></h3>
              <div class="flex items-center gap-1.5">
                 <span class="text-[10px] font-bold text-slate-500">De {{ productsCountComputed }} en catálogo</span>
              </div>
           </div>
        </div>

        <div class="group bg-white rounded-[24px] p-5 border border-white shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden flex items-center gap-4">
           <div class="w-14 h-14 bg-amber-50 rounded-2xl flex-shrink-0 flex items-center justify-center text-amber-600 border border-amber-100 shadow-inner">
             <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
             </svg>
           </div>
 
           <div class="relative z-10 flex-1">
              <div class="flex justify-between items-center mb-0.5">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Efectivo</p>
                <span :class="hasOpenSession ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500'" 
                       class="px-1.5 py-0.5 text-[8px] font-black uppercase rounded-md">
                   {{ hasOpenSession ? 'ON' : 'OFF' }}
                 </span>
              </div>
              <h3 class="text-2xl font-black text-slate-900 tracking-tighter leading-none mb-1">
                 ${{ hasOpenSession && currentSession ? formatCurrency(realCashAvailable) : '0' }}
              </h3>
              <p class="text-[10px] font-bold text-emerald-600">Disponible cierre</p>
           </div>
        </div>

        <div 
          id="tour-alertas-stock"
          class="group bg-white rounded-[24px] p-5 border shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden flex items-center gap-4"
          :class="lowStockComputed.length > 0 ? 'border-rose-200' : 'border-white'">
           
           <div class="w-14 h-14 rounded-2xl flex-shrink-0 flex items-center justify-center transition-colors duration-300 border shadow-inner"
                :class="lowStockComputed.length > 0 ? 'bg-rose-50 text-rose-600 border-rose-100' : 'bg-emerald-50 text-emerald-600 border-emerald-100'">
             <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
             </svg>
           </div>
 
           <div class="relative z-10 flex-1">
              <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Alertas Stock</p>
              <h3 class="text-2xl font-black tracking-tighter leading-none mb-1" :class="lowStockComputed.length > 0 ? 'text-rose-600' : 'text-slate-900'">
                {{ lowStockComputed.length }}
              </h3>
              <div class="flex items-center gap-2 text-[10px] font-bold rounded-lg w-fit"
                   :class="lowStockComputed.length > 0 ? 'text-rose-600' : 'text-emerald-600'">
                 {{ lowStockComputed.length > 0 ? 'Requiere Atención' : 'Stock Saludable' }}
              </div>
           </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <div id="tour-analisis-ingresos" class="lg:col-span-8 bg-white rounded-[24px] p-6 border border-white shadow-sm flex flex-col relative overflow-hidden">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4 relative z-10">
             <div>
                <h3 class="text-lg font-black text-slate-900">Análisis de Ingresos</h3>
                <p class="text-xs text-slate-400 font-bold mt-1">Tendencia de ventas en el tiempo</p>
             </div>
             <div class="flex items-center bg-slate-100 rounded-xl p-1 border border-slate-200">
                <button v-for="period in [{ value: 'today', label: '24H' }, { value: 'week', label: '7D' }, { value: 'month', label: '30D' }]" 
                        :key="period.value"
                        @click="changePeriod(period.value)"
                        :class="['px-4 py-1.5 text-xs font-bold rounded-[10px] transition-all', selectedPeriod === period.value ? 'bg-white text-indigo-700 shadow-sm ring-1 ring-black/5' : 'text-slate-400 hover:text-slate-600']">
                   {{ period.label }}
                </button>
             </div>
          </div>

          <div class="flex gap-8 mb-6 z-10">
             <div>
                <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider mb-1">Volumen Total</p>
                <p class="text-3xl font-black text-slate-900 tracking-tight">
                   ${{ weeklyDataComputed.length > 0 ? (weeklyDataComputed.reduce((sum, day) => sum + (day.sales || 0), 0) / 1000000).toFixed(2) + 'M' : '0.00' }}
                </p>
             </div>
             <div>
                <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider mb-1">Crecimiento</p>
                <div class="flex items-center gap-1 text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg w-fit">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    <p class="text-sm font-black">+{{ trendsComputed.growth }}%</p>
                </div>
             </div>
          </div>

          <div class="h-[350px] w-full relative z-0">
             <Line :data="lineChartData" :options="lineChartOptions" />
          </div>
        </div>

        <div class="lg:col-span-4 bg-white rounded-[24px] p-6 border border-white shadow-sm flex flex-col">
          <div class="mb-6">
              <h3 class="text-lg font-black text-slate-900">Top Productos</h3>
              <p class="text-xs text-slate-400 font-bold mt-1">Distribución de ingresos</p>
          </div>
          
          <div class="flex-grow flex items-center justify-center relative min-h-[220px]">
             <div class="w-[200px] h-[200px] relative">
                <Doughnut :data="doughnutChartData" :options="doughnutChartOptions" />
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                   <span class="text-4xl font-black text-slate-800 tracking-tighter">{{ topProductsComputed.length }}</span>
                   <span class="text-[10px] text-slate-400 uppercase font-bold tracking-widest mt-1">Items</span>
                </div>
             </div>
          </div>

          <div class="mt-6 space-y-3">
             <div v-for="(product, index) in topProductsComputed.slice(0, 3)" :key="index" 
                  class="flex items-center justify-between p-3 bg-slate-50/50 hover:bg-slate-50 rounded-xl transition-colors border border-transparent hover:border-slate-200 group">
                <div class="flex items-center gap-3">
                   <div :style="{ backgroundColor: doughnutColors[index] }" class="w-3 h-3 rounded-full shadow-sm ring-2 ring-white"></div>
                   <span class="text-xs font-bold text-slate-600 truncate w-32" :title="product.name">{{ product.name }}</span>
                </div>
                <span class="text-xs font-black text-slate-900 bg-white px-2 py-1 rounded-md shadow-sm border border-slate-100">${{ formatCurrency(product.revenue) }}</span>
             </div>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-[24px] border border-white shadow-sm overflow-hidden">
         <div class="p-6 border-b border-slate-50 flex items-center justify-between">
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
               Actividad Reciente
               <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
            </h3>
         </div>
         
         <div class="grid grid-cols-1 lg:grid-cols-2 divide-y lg:divide-y-0 lg:divide-x divide-slate-50">
             <div class="p-6">
                <h4 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-4">Transacciones recientes</h4>
                <div class="space-y-3">
                   <div v-for="sale in recentSalesComputed.slice(0, 3)" :key="sale.id" 
                        class="flex items-center justify-between p-3 rounded-2xl hover:bg-slate-50 transition-all border border-transparent hover:border-slate-200 cursor-pointer group">
                      <div class="flex items-center gap-4">
                         <div class="w-10 h-10 rounded-xl bg-slate-50 text-slate-500 flex items-center justify-center font-bold text-[10px] group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                            #{{ sale.id }}
                         </div>
                         <div>
                            <p class="text-sm font-bold text-slate-900 group-hover:text-indigo-600 transition-colors">Venta #{{ sale.id }}</p>
                            <p class="text-[11px] text-slate-400 font-bold">{{ formatDateTime(sale.date) }}</p>
                         </div>
                      </div>
                      <div class="text-right">
                         <p class="text-sm font-black text-slate-900">${{ (sale.total).toFixed(2) }}</p>
                         <span class="text-[10px] font-bold text-emerald-600 flex items-center justify-end gap-1">
                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Pagado
                         </span>
                      </div>
                   </div>
                </div>
             </div>
             
             <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Reposición Urgente</h4>
                    <span v-if="lowStockComputed.length > 0" class="bg-rose-50 text-rose-600 px-2 py-1 rounded-md text-[10px] font-black border border-rose-100">
                        {{ lowStockComputed.length }} ITEMS
                    </span>
                </div>
                
                <div v-if="lowStockComputed.length === 0" class="h-full flex flex-col items-center justify-center text-center py-8 opacity-40">
                   <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mb-2">
                       <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                   </div>
                   <p class="text-xs font-bold text-slate-500">Todo en orden</p>
                </div>

                <div v-else class="space-y-3">
                   <div v-for="product in lowStockComputed.slice(0, 3)" :key="product.id" 
                        class="flex items-center gap-4 p-3 rounded-2xl bg-white hover:bg-rose-50 border border-slate-100 hover:border-rose-200 transition-colors group">
                      <div class="w-10 h-10 rounded-lg bg-slate-100 border border-slate-200 flex-shrink-0 overflow-hidden relative">
                         <img :src="getProductImageSync(product)" class="w-full h-full object-cover" @error="handleImageError" alt="img">
                         <div class="absolute inset-0 bg-black/5 group-hover:bg-transparent transition-colors"></div>
                      </div>
                      <div class="flex-1 min-w-0">
                         <p class="text-xs font-bold text-slate-900 truncate group-hover:text-rose-700 transition-colors">{{ product.name }}</p>
                         <p class="text-[10px] text-rose-500 font-black mt-0.5">Quedan: {{ product.stock }} unidades</p>
                      </div>
                      <button class="w-8 h-8 rounded-lg flex items-center justify-center bg-slate-50 hover:bg-indigo-600 text-slate-400 hover:text-white transition-all border border-slate-200 hover:border-indigo-600">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                      </button>
                   </div>
                </div>
             </div>
         </div>
      </div>

    <ToastNotifications ref="toastRef" @action-click="handleAlertAction" @dismiss-forever="handleDismissForever" />
    </div>
  </div>
</template>

<script setup>
import { computed, ref, onMounted, nextTick, watch } from 'vue'
import ToastNotifications from './ToastNotifications.vue'
import ContextualTour from './ContextualTour.vue'
import authService from '../services/authService.js'
import { reportsService } from '../services/reportsService.js'
import { productsService } from '../services/productsService.js'
import { useCashSession } from '../services/cashSessionService.js'
import { apiCall } from '../services/api.js'
import { cashReportsService } from '../services/cashReportsService.js' // NUEVO: Para datos por horas

// Dependencias de Chart.js
import { Line, Doughnut } from 'vue-chartjs'
import {
  Chart,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  ArcElement,
  Title,
  Tooltip,
  Legend,
  Filler
} from 'chart.js'
Chart.register(CategoryScale, LinearScale, PointElement, LineElement, ArcElement, Title, Tooltip, Legend, Filler)

// 🎯 Tour Steps del Dashboard  
const tourSteps = ref([
  {
    selector: '#tour-ia-button',
    title: 'Asistente Inteligente 105 IA',
    content: `
      <div class="space-y-2">
        <p class="text-sm text-gray-700">
          Tu <strong class="text-purple-600">asistente personal impulsado por IA</strong> 
          que puede ayudarte con:
        </p>
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-2">
          <ul class="text-xs text-purple-900 space-y-1">
            <li>• <strong>Análisis de datos</strong> y tendencias de ventas</li>
            <li>• <strong>Generación de reportes</strong> personalizados</li>
            <li>• <strong>Respuestas instantáneas</strong> sobre tu negocio</li>
            <li>• <strong>Recomendaciones</strong> para mejorar ventas</li>
          </ul>
        </div>
      </div>
    `
  },
  {
    selector: '#tour-voice-button',
    title: 'Radio Corporativa',
    content: `
      <div class="space-y-2">
        <p class="text-sm text-gray-700">
          ¡Sintoniza nuestra <strong class="text-emerald-600">radio exclusiva</strong>! 
        </p>
        <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-2">
          <p class="text-xs text-emerald-900 font-medium mb-1">Música para trabajar:</p>
          <ul class="text-xs text-emerald-800 space-y-0.5">
            <li>• Energía positiva para tu jornada</li>
            <li>• Reproducción continua en segundo plano</li>
            <li>• Control fácil desde el header</li>
          </ul>
        </div>
      </div>
    `
  },
  {
    selector: '#tour-help-button',
    title: 'Centro de Ayuda',
    content: `
      <div class="space-y-2">
        <p class="text-sm text-gray-700">
          Accede a <strong class="text-blue-600">documentación completa</strong> del sistema.
        </p>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-2">
          <ul class="text-xs text-blue-900 space-y-1">
            <li>• Tutoriales paso a paso</li>
            <li>• Guías de funciones avanzadas</li>
            <li>• Solución de problemas comunes</li>
            <li>• Siempre disponible cuando lo necesites</li>
          </ul>
        </div>
      </div>
    `
  },
  {
    selector: '#tour-nueva-venta-btn',
    title: 'Botón Nueva Venta',
    content: `
      <div class="space-y-2">
        <p class="text-sm text-gray-700">
          <strong class="text-green-600">Inicia una nueva transacción</strong> con un solo clic.
        </p>
        <div class="bg-green-50 border border-green-200 rounded-lg p-2">
          <p class="text-xs text-green-900 font-medium mb-1">Te llevará al POS para:</p>
          <ul class="text-xs text-green-800 space-y-0.5">
            <li>• Escanear o buscar productos</li>
            <li>• Procesar pagos (efectivo, tarjeta, mixto)</li>
            <li>• Imprimir o enviar facturas por WhatsApp</li>
            <li>• Gestionar clientes y descuentos</li>
          </ul>
        </div>
      </div>
    `
  },
  {
    selector: '#tour-estado-caja',
    title: 'Estado de Caja',
    content: `
      <div class="space-y-2">
        <p class="text-sm text-gray-700">
          Muestra el <strong class="text-amber-600">estado actual de tu caja</strong>.
        </p>
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-2">
          <p class="text-xs text-amber-900 font-medium mb-1">Importante saber:</p>
          <ul class="text-xs text-amber-800 space-y-0.5">
            <li>• <strong>Abierta:</strong> Puedes realizar ventas</li>
            <li>• <strong>Cerrada:</strong> Debes abrir caja primero</li>
            <li>• Muestra el total acumulado del turno</li>
            <li>• Facilita el cuadre al final del día</li>
          </ul>
        </div>
      </div>
    `
  },
  {
    selector: '#tour-analisis-ingresos',
    title: 'Análisis de Ingresos',
    content: `
      <div class="space-y-2">
        <p class="text-sm text-gray-700">
          <strong class="text-indigo-600">Gráfico interactivo</strong> de tus ventas en tiempo real.
        </p>
        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-2">
          <p class="text-xs text-indigo-900 font-medium mb-1">3 vistas disponibles:</p>
          <ul class="text-xs text-indigo-800 space-y-0.5">
            <li>• <strong>24 Horas:</strong> Ventas hora por hora</li>
            <li>• <strong>7 Días:</strong> Tendencia semanal</li>
            <li>• <strong>30 Días:</strong> Análisis mensual</li>
          </ul>
          <p class="text-xs text-indigo-700 mt-1 italic">
            Usa estos datos para identificar tus mejores horarios y días de venta.
          </p>
        </div>
      </div>
    `
  },
  {
    selector: '#tour-alertas-stock',
    title: 'Alertas de Stock',
    content: `
      <div class="space-y-2">
        <p class="text-sm text-gray-700">
          <strong class="text-red-600">Sistema de alertas automático</strong> para inventario.
        </p>
        <div class="bg-red-50 border border-red-200 rounded-lg p-2">
          <p class="text-xs text-red-900 font-medium mb-1">Te avisa cuando:</p>
          <ul class="text-xs text-red-800 space-y-0.5">
            <li>• Un producto tiene stock bajo (menos de 10)</li>
            <li>• Necesitas reabastecer urgentemente</li>
            <li>• Hay productos a punto de agotarse</li>
          </ul>
          <p class="text-xs text-red-700 mt-1 italic">
            ¡Evita quedarte sin productos populares!
          </p>
        </div>
      </div>
    `
  }
])

// 🎓 Control del Tour del Dashboard
const DEV_MODE_DASHBOARD = false // false = Tour solo primera vez | true = Tour siempre
const isFirstVisitDashboard = ref(DEV_MODE_DASHBOARD || !localStorage.getItem('dashboard_tour_completed'))
const dashboardTourRef = ref(null)

const handleTourComplete = () => {
  console.log('✅ Dashboard tour completado')
  if (!DEV_MODE_DASHBOARD) {
    localStorage.setItem('dashboard_tour_completed', 'true')
    isFirstVisitDashboard.value = false
  }
}

const handleTourSkip = () => {
  console.log('⏭️ Dashboard tour omitido')
  if (!DEV_MODE_DASHBOARD) {
    localStorage.setItem('dashboard_tour_completed', 'true')
    isFirstVisitDashboard.value = false
  }
}

// 🏦 Composable para manejo de sesiones de caja
const { 
  currentSession, 
  hasOpenSession, 
  isLoading: cashLoading,
  openSession,
  closeSession,
  loadCurrentSession
} = useCashSession()

const props = defineProps({
  salesData: { 
    type: Object, 
    default: () => ({
      today: { revenue: 0, sales: 0, items_sold: 0, average_ticket: 0 }
    })
  },
  productsCount: { type: Number, default: 0 },
  lowStock: { 
    type: Array, 
    default: () => []
  },
  recentSales: { 
    type: Array, 
    default: () => []
  },
  notifications: { type: Array, default: () => [] }
})

const selectedPeriod = ref('today') // Comenzar con vista de 24 horas
const loadingChart = ref(false) // Para el spinner del botón actualizar
const toastRef = ref(null)

// Estados reactivos para datos del dashboard
const dashboardData = ref({
  summary: {
    today_sales: { amount: 0, count: 0 },
    month_sales: { amount: 0, count: 0 },
    low_stock_products: 0,
    total_products: 0,
    total_customers: 0
  },
  charts: {
    daily_sales: [],
    hourly_sales: [], // NUEVO: Datos por horas para gráfico 24H
    top_categories: [],
    top_products: [],
    inventory_by_category: [],
    low_stock_products: [] // Productos reales con stock bajo
  }
})

// Estados para sesiones activas multi-usuario
const activeSessions = ref([])
const activeSessionsCount = computed(() => activeSessions.value.length)

// Estado para manejo de imágenes de productos
const productImages = ref(new Map())
const loadingImages = ref(new Set())

const loading = ref(true)
const error = ref(null)

// 🖼️ Función utilitaria para manejo inteligente de imágenes (mejorada con BD)
const getProductImage = async (product) => {
  if (!product) {
    return null
  }
  
  // Verificar cache de imágenes
  if (productImages.value.has(product.id)) {
    const cachedImage = productImages.value.get(product.id)
    return cachedImage
  }
  
  // Si ya está cargando, evitar múltiples llamadas
  if (loadingImages.value.has(product.id)) {
    return null
  }
  
  // Si ya tiene los datos completos con imagen, usarlos
  if (product.image_url || product.imageUrl || product.image || product.img) {
    const imageUrl = product.image_url || 
                    product.imageUrl || 
                    product.image || 
                    product.img || 
                    product.photo || 
                    product.picture || 
                    product.thumbnail
    
    productImages.value.set(product.id, imageUrl)
    return imageUrl || null
  }
  
  // Si no tiene imagen, buscar en la base de datos completa
  try {
    loadingImages.value.add(product.id)
    const productsResponse = await productsService.getAll()
    
    if (productsResponse.success && productsResponse.data?.products) {
      const fullProduct = productsResponse.data.products.find(p => p.id === product.id)
      
      if (fullProduct) {
        const imageUrl = fullProduct.image_url || 
                        fullProduct.imageUrl || 
                        fullProduct.image || 
                        fullProduct.img || 
                        fullProduct.photo || 
                        fullProduct.picture || 
                        fullProduct.thumbnail
        
        productImages.value.set(product.id, imageUrl)
        loadingImages.value.delete(product.id)
        return imageUrl || null
      }
    }
  } catch (error) {
    console.error('Error obteniendo imagen del producto:', error)
  } finally {
    loadingImages.value.delete(product.id)
  }
  
  productImages.value.set(product.id, null)
  return null
}

// 🖼️ Función sincrónica para obtener imagen del cache o placeholder
const getProductImageSync = (product) => {
  if (!product) return null
  
  // Verificar cache primero
  if (productImages.value.has(product.id)) {
    return productImages.value.get(product.id)
  }
  
  // Si el producto ya tiene imagen, usarla directamente sin esperar al cache
  const directImage = product.image_url || 
                     product.imageUrl || 
                     product.image || 
                     product.img || 
                     product.photo || 
                     product.picture || 
                     product.thumbnail
  
  if (directImage) {
    productImages.value.set(product.id, directImage)
    return directImage
  }
  
  // Si no está en cache ni tiene imagen directa, cargar de forma asíncrona
  getProductImage(product)
  
  // Retornar placeholder mientras carga
  return null
}

// �️ Función para pre-cargar imágenes de productos
const preloadProductImages = async () => {
  try {
    
    // Obtener todos los productos únicos del dashboard
    const allProducts = []
    
    // Productos de top ventas
    if (dashboardData.value.charts.top_products) {
      allProducts.push(...dashboardData.value.charts.top_products)
    }
    
    // Productos de stock bajo
    if (dashboardData.value.charts.low_stock_products) {
      allProducts.push(...dashboardData.value.charts.low_stock_products)
    }
    
    // Eliminar duplicados por ID
    const uniqueProducts = allProducts.filter((product, index, self) => 
      index === self.findIndex(p => p.id === product.id)
    )
    
    
    // Pre-cargar imágenes en paralelo
    const imagePromises = uniqueProducts.map(product => getProductImage(product))
    await Promise.allSettled(imagePromises)
    
  } catch (error) {
    console.error('🖼️ Error en pre-carga de imágenes:', error)
  }
}

// �🚨 Manejar errores de carga de imagen
const handleImageError = (event) => {
  event.target.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDIwMCAyMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIyMDAiIGhlaWdodD0iMjAwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik04MCA5MEg4MC4wMUg4MFpNNzAgMTAwSDcwLjAxSDcwWk05NiA3MEw4MC41OSA4NS40MVY4NS40MUw3NiA5MEw4MCA5NEw4NCA5MEw5MCA4NEwxMDQgNzBIOTZaTTYwIDQwSDE0MEM2NC40MTgzIDQwIDY4IDQzLjU4MTcgNjggNDhWNTJIMTMyVjQ4QzEzMiA0My41ODE3IDEzNS41ODIgNDAgMTQwIDQwVjQwQzE0NC40MTggNDAgMTQ4IDQzLjU4MTcgMTQ4IDQ4VjE1MkMxNDggMTU2LjQxOCAxNDQuNDE4IDE2MCAxNDAgMTYwSDYwQzU1LjU4MTcgMTYwIDUyIDE1Ni40MTggNTIgMTUyVjQ4QzUyIDQzLjU4MTcgNTUuNTgxNyA0MCA2MCA0MFpNNjAgNTJIMTQwVjE1Mkg2MFY1MloiIGZpbGw9IiM5Q0E0QUYiLz4KPC9zdmc+'
}

// Función para cargar datos reales usando los mismos servicios que reportes
const loadDashboardData = async (period = 'week') => {
  try {
    loading.value = true
    error.value = null
    
    // LÓGICA MEJORADA: Manejar período de 24 horas con datos por horas
    if (period === 'today') {
      await loadHourlyData()
      return
    }
    
    // Mapear períodos del dashboard a períodos de la API
    const periodMap = {
      'week': 'week',      // 7 días
      'month': 'month',    // 30 días  
      'year': 'year'       // 1 año
    }
    const apiPeriod = periodMap[period] || 'week'
    
    // Cargar datos usando los mismos servicios que reportes - UNO A LA VEZ para detectar errores
    const todaySalesData = await reportsService.getSalesData('today')
    
    const weekSalesData = await reportsService.getSalesData(apiPeriod)
    
    const dailySalesData = await reportsService.getDailySales(apiPeriod)
    
    const topProductsData = await reportsService.getTopProducts('month', 5)
    
    const lowStockData = await reportsService.getLowStockProducts()
    
    const productStatsData = await reportsService.getProductStats()
    
    // Actualizar datos del dashboard - DIRECTAMENTE usando los datos de reportes
    if (todaySalesData.success && todaySalesData.data) {
      console.log('🏠 ✅ NUEVA LÓGICA FUNCIONANDO - Ventas procesadas correctamente')
      
      // Obtener ventas reales de hoy (hora Colombia)
      const ventasHoyColombia = await reportsService.getVentasHoyColombia()

      // Sobrescribir la métrica de ventas hoy con el valor real
      dashboardData.value.summary.today_sales = {
        amount: ventasHoyColombia.total || 0,
        count: ventasHoyColombia.transacciones || 0
      }
    }
    
    if (weekSalesData.success && weekSalesData.data) {
      dashboardData.value.summary.month_sales = {
        amount: weekSalesData.data.totalSales || 0,
        count: weekSalesData.data.totalTransactions || 0
      }
    }
    
    if (weekSalesData.success && weekSalesData.data) {
      dashboardData.value.summary.month_sales = {
        amount: weekSalesData.data.totalSales || 0,
        count: weekSalesData.data.totalTransactions || 0
      }
    }
    
    if (dailySalesData.success && Array.isArray(dailySalesData.data)) {
      
      // Usar datos reales con fechas si están disponibles
      if (dailySalesData.dataWithDates && Array.isArray(dailySalesData.dataWithDates)) {
        dashboardData.value.charts.daily_sales = dailySalesData.dataWithDates.map(item => ({
          date: item.date, // Fecha real ISO (YYYY-MM-DD)
          dateObject: item.dateObject,
          day: item.dateObject.toLocaleDateString('es-ES', { weekday: 'short' }),
          sales: item.total || 0
        }))
      } else {
        // Fallback: usar datos sin fechas (compatibilidad) - Corregido para zona horaria Colombia
        dashboardData.value.charts.daily_sales = dailySalesData.data.map((sales, index) => {
          // Crear fecha correcta para zona horaria de Colombia (UTC-5)
          const now = new Date()
          // Ajustar a zona horaria de Colombia
          const colombiaTime = new Date(now.getTime() - (5 * 60 * 60 * 1000))
          
          // Calcular la fecha correspondiente al índice
          const targetDate = new Date(colombiaTime)
          targetDate.setDate(targetDate.getDate() - (dailySalesData.data.length - 1 - index))
          
          return {
            date: targetDate.toISOString().split('T')[0],
            day: targetDate.toLocaleDateString('es-ES', { weekday: 'short' }),
            sales: sales || 0,
            dateObject: targetDate
          }
        })
      }
      
    }
    
    if (topProductsData.success && Array.isArray(topProductsData.data)) {
      // Mantener los datos originales de la API sin mapear para evitar confusión
      dashboardData.value.charts.top_products = topProductsData.data
    }
    
    if (lowStockData.success && Array.isArray(lowStockData.data)) {
      // Guardar tanto la cantidad como los productos reales
      dashboardData.value.summary.low_stock_products = lowStockData.data.length
      dashboardData.value.charts.low_stock_products = lowStockData.data // Productos reales
    }
    
    if (productStatsData.success) {
      dashboardData.value.summary.total_products = productStatsData.data.totalProducts
      // Verificar consistencia de stock bajo
      if (lowStockData.success) {
      }
    }
    
    
    // Pre-cargar imágenes de productos
    await preloadProductImages()
    
    // Cargar últimas transacciones reales
    await loadRecentTransactions()
    
    // Forzar actualización de reactividad
    await nextTick()
    
  } catch (err) {
    console.error('❌ Error cargando datos del dashboard:', err)
    error.value = err.message || 'Error desconocido al cargar datos del dashboard'
  } finally {
    loading.value = false
  }
}

// NUEVA FUNCIÓN: Cargar datos por horas para vista 24H
const loadHourlyData = async () => {
  try {
    console.log('🕐 Cargando datos por horas...')
    
    // Cargar datos básicos del día
    const todaySalesData = await reportsService.getSalesData('today')
    // Obtener ventas reales de hoy (hora Colombia)
    const ventasHoyColombia = await reportsService.getVentasHoyColombia()
    
    // Cargar datos por horas desde el servicio de reportes de caja
    const hourlyResponse = await cashReportsService.getHourlyEfficiency('today')
    
    // Cargar otros datos necesarios
    const topProductsData = await reportsService.getTopProducts('today', 5)
    const lowStockData = await reportsService.getLowStockProducts()
    const productStatsData = await reportsService.getProductStats()
    
    // Actualizar summary con datos de hoy (siempre sobrescribir con valor real de Colombia)
    dashboardData.value.summary.today_sales = {
      amount: ventasHoyColombia.total || 0,
      count: ventasHoyColombia.transacciones || 0
    }
    // También actualizar month_sales para mantener consistencia visual
    if (todaySalesData.success && todaySalesData.data) {
      dashboardData.value.summary.month_sales = {
        amount: todaySalesData.data.totalSales || 0,
        count: todaySalesData.data.totalTransactions || 0
      }
    }
    
    // Procesar datos por horas
    if (hourlyResponse.success && hourlyResponse.data && hourlyResponse.data.hourly_data) {
      const hourlyData = hourlyResponse.data.hourly_data
      
      // Transformar datos por horas para el gráfico
      dashboardData.value.charts.hourly_sales = hourlyData.map(hour => ({
        name: hour.hour, // "08:00", "09:00", etc.
        sales: hour.sales || 0,
        transactions: hour.transactions || 0
      }))
      
      console.log('✅ Datos por horas cargados:', {
        totalHours: hourlyData.length,
        hoursWithSales: hourlyData.filter(h => h.sales > 0).length,
        totalSales: hourlyResponse.data.total_sales,
        peakHour: hourlyResponse.data.peak_hour
      })
    } else {
      console.warn('⚠️ No se pudieron cargar datos por horas')
      dashboardData.value.charts.hourly_sales = []
    }
    
    // Actualizar otros datos
    if (topProductsData.success && Array.isArray(topProductsData.data)) {
      dashboardData.value.charts.top_products = topProductsData.data
    }
    
    if (lowStockData.success && Array.isArray(lowStockData.data)) {
      dashboardData.value.summary.low_stock_products = lowStockData.data.length
      dashboardData.value.charts.low_stock_products = lowStockData.data
    }
    
    if (productStatsData.success) {
      dashboardData.value.summary.total_products = productStatsData.data.totalProducts
    }
    
    // Pre-cargar imágenes de productos
    await preloadProductImages()
    
    // Cargar últimas transacciones reales
    await loadRecentTransactions()
    
    // Forzar actualización de reactividad
    await nextTick()
    
  } catch (err) {
    console.error('❌ Error cargando datos por horas:', err)
    error.value = err.message || 'Error cargando datos por horas'
  }
}

// Computed para datos calculados - USANDO DASHBOARDDATA INTERNO
const salesDataComputed = computed(() => {
  const todayAmount = dashboardData.value.summary?.today_sales?.amount || 0
  const todayCount = dashboardData.value.summary?.today_sales?.count || 0
  const topProducts = dashboardData.value.charts?.top_products || []
  const itemsSold = topProducts.reduce((sum, p) => sum + (p.sold || 0), 0)
  
  
  return {
    today: {
      revenue: todayAmount,
      sales: todayCount,
      items_sold: itemsSold,
      average_ticket: todayCount > 0 ? todayAmount / todayCount : 0
    }
  }
})

// Computed para efectivo real disponible en caja
const realCashAvailable = computed(() => {
  if (!hasOpenSession.value || !currentSession.value) return 0
  
  const opening = parseFloat(currentSession.value.opening_amount || 0)
  const sales = parseFloat(currentSession.value.total_sales || 0)
  const expenses = parseFloat(currentSession.value.total_expenses || 0)
  
  return opening + sales - expenses
})

// Computed para tendencias de crecimiento
const trendsComputed = computed(() => {
  const dailySales = dashboardData.value.charts?.daily_sales || []
  
  if (dailySales.length < 2) {
    return { growth: '+0.0' }
  }
  
  // Calcular crecimiento comparando últimos días vs días anteriores
  const midPoint = Math.floor(dailySales.length / 2)
  const recentPeriod = dailySales.slice(midPoint)
  const previousPeriod = dailySales.slice(0, midPoint)
  
  const recentAvg = recentPeriod.reduce((sum, day) => {
    const value = (typeof day === 'object' && day !== null) ? (day.sales || 0) : (day || 0)
    return sum + value
  }, 0) / recentPeriod.length
  
  const previousAvg = previousPeriod.reduce((sum, day) => {
    const value = (typeof day === 'object' && day !== null) ? (day.sales || 0) : (day || 0)
    return sum + value
  }, 0) / previousPeriod.length
  
  let growthRate = 0
  if (previousAvg > 0) {
    growthRate = ((recentAvg - previousAvg) / previousAvg) * 100
  }
  
  return {
    growth: growthRate > 0 ? `+${growthRate.toFixed(1)}` : growthRate.toFixed(1)
  }
})

const productsCountComputed = computed(() => {
  const count = dashboardData.value.summary?.total_products || 0
  return count
})

const lowStockComputed = computed(() => {
  const realProducts = dashboardData.value.charts?.low_stock_products || []
  // Hacer que el computed reaccione a cambios en productImages
  const imagesCache = productImages.value
  
  // Si tenemos productos reales, usarlos
  if (realProducts.length > 0) {
    return realProducts.map(product => ({
      id: product.id,
      name: product.name || 'Producto sin nombre',
      category: product.category || 'General',
      stock: product.stock || product.current_stock || 0,
      min_stock: product.min_stock || 5,
      image: getProductImageSync(product), // Usar función sincrónica de imágenes
      // Incluir el objeto original para debug si es necesario
      original: product
    }))
  }
  
  // Fallback: usar datos ficticios si no hay datos reales
  const stockCount = dashboardData.value.summary?.low_stock_products || 0
  return Array.from({ length: stockCount }, (_, i) => ({
    id: i + 1,
    name: `Producto con stock bajo ${i + 1}`,
    category: 'General',
    stock: 1, // Stock bajo real
    image: null // Fallback a placeholder en template
  }))
})

const weeklyDataComputed = computed(() => {
  // NUEVO: Manejar datos por horas para período "today"
  if (selectedPeriod.value === 'today') {
    const hourlyData = dashboardData.value.charts?.hourly_sales || []
    
    if (!hourlyData.length) {
      // Datos por defecto de 24 horas si no hay datos reales
      return Array.from({ length: 24 }, (_, i) => ({
        name: `${i.toString().padStart(2, '0')}:00`,
        sales: 0,
        transactions: 0
      }))
    }
    
    // Usar datos por horas reales
    return hourlyData.map(hour => ({
      name: hour.name, // "08:00", "09:00", etc.
      sales: hour.sales || 0,
      transactions: hour.transactions || 0
    }))
  }
  
  // RESTO: Lógica existente para períodos de días/semanas/meses
  const dailySales = dashboardData.value.charts?.daily_sales || []
  
  if (!dailySales.length) {
    // Datos por defecto si no hay datos reales
    return [
      { name: 'Lun', sales: 0 },
      { name: 'Mar', sales: 0 },
      { name: 'Mié', sales: 0 },
      { name: 'Jue', sales: 0 },
      { name: 'Vie', sales: 0 },
      { name: 'Sáb', sales: 0 },
      { name: 'Dom', sales: 0 }
    ]
  }
  
  // Para períodos largos, reducir la cantidad de puntos de datos
  if (selectedPeriod.value === 'year' && dailySales.length > 30) {
    // Para 1 año: agrupar por meses (exactamente 12 meses)
    const months = []
    const monthsToShow = 12
    const daysPerMonth = Math.floor(dailySales.length / monthsToShow)
    
    for (let i = 0; i < monthsToShow; i++) {
      const startIndex = i * daysPerMonth
      const endIndex = (i === monthsToShow - 1) ? dailySales.length : (i + 1) * daysPerMonth
      const monthData = dailySales.slice(startIndex, endIndex)
      
      const monthTotal = monthData.reduce((sum, day) => {
        const value = (typeof day === 'object' && day !== null) ? (day.sales || 0) : (day || 0)
        return sum + value
      }, 0)
      
      // Generar nombres de meses más realistas
      const monthNames = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic']
      const currentMonth = new Date().getMonth()
      const monthIndex = (currentMonth - (monthsToShow - 1 - i) + 12) % 12
      
      months.push({
        name: monthNames[monthIndex],
        sales: monthTotal
      })
    }
    return months
  } else if (selectedPeriod.value === 'month' && dailySales.length > 14) {
    // Para 30 días: mostrar TODOS los días (no filtrar cada 3)
    // Debug logs removed for production
    
    return dailySales.map((day, index) => {
      // Usar fecha real del objeto si existe
      let displayName = ''
      let salesValue = 0
      
      if (day.dateObject && day.dateObject instanceof Date) {
        displayName = day.dateObject.toLocaleDateString('es-ES', { day: 'numeric', month: 'short' })
        salesValue = day.sales || 0
        // Debug logs removed for production
      } else if (day.date) {
        // Si no hay dateObject pero sí hay fecha string
        const dateObj = new Date(day.date + 'T12:00:00')
        displayName = dateObj.toLocaleDateString('es-ES', { day: 'numeric', month: 'short' })
        salesValue = day.sales || day.total || 0
        // Debug logs removed for production
      } else {
        // Fallback: calcular fecha manualmente con zona horaria de Colombia
        const now = new Date()
        // Ajustar a zona horaria de Colombia (UTC-5)
        const colombiaTime = new Date(now.getTime() - (5 * 60 * 60 * 1000))
        
        // Calcular fecha correcta basada en la posición en el array
        const targetDate = new Date(colombiaTime)
        targetDate.setDate(targetDate.getDate() - dailySales.length + index + 1)
        
        displayName = targetDate.toLocaleDateString('es-ES', { day: 'numeric', month: 'short' })
        salesValue = (typeof day === 'object' && day !== null) ? (day.sales || 0) : (day || 0)
        // Debug logs removed for production
      }
      
      return {
        name: displayName,
        sales: salesValue
      }
    })
  }
  
  // Para semana: mostrar días normalmente usando fechas reales
  // Debug logs removed for production
  
  return dailySales.map((day, index) => {
    // Manejar tanto objetos como números
    let salesValue = 0
    let dayName = ''
    
    if (typeof day === 'object' && day !== null) {
      // Si es un objeto con propiedades
      salesValue = day.sales || 0
      
      // Usar fecha real si está disponible
      if (day.dateObject && day.dateObject instanceof Date) {
        dayName = day.dateObject.toLocaleDateString('es-ES', { weekday: 'short' })
      } else if (day.date) {
        const dateObj = new Date(day.date + 'T12:00:00')
        dayName = dateObj.toLocaleDateString('es-ES', { weekday: 'short' })
      } else {
        dayName = day.day || day.name || ''
      }
    } else {
      // Si es un número directo
      salesValue = day || 0
    }
    
    // Si no tenemos nombre del día, calcularlo como fallback con zona horaria Colombia
    if (!dayName) {
      const now = new Date()
      // Ajustar a zona horaria de Colombia (UTC-5)
      const colombiaTime = new Date(now.getTime() - (5 * 60 * 60 * 1000))
      
      // Calcular fecha correcta
      const targetDate = new Date(colombiaTime)
      targetDate.setDate(targetDate.getDate() - dailySales.length + index + 1)
      dayName = targetDate.toLocaleDateString('es-ES', { weekday: 'short' })
    }
    
    return {
      name: dayName,
      sales: salesValue
    }
  })
})

const topProductsComputed = computed(() => {
  const products = dashboardData.value.charts?.top_products || []
  // Hacer que el computed reaccione a cambios en productImages
  const imagesCache = productImages.value
  
  if (!products.length) {
    return []
  }
  
  
  return products.slice(0, 5).map(product => ({
    id: product.id, // Incluir ID para identificación
    name: product.name || 'Producto sin nombre',
    sold: product.sold || 0,
    revenue: product.revenue || 0,
    image: getProductImageSync(product), // Usar función sincrónica de imágenes
    // Incluir el objeto original para debug si es necesario
    original: product
  }))
})

// Estado reactivo para transacciones recientes reales
const recentSales = ref([])

// Función para cargar últimas transacciones reales
const loadRecentTransactions = async () => {
  try {
    const response = await reportsService.getRecentTransactions(4)
    
    if (response.success) {
      recentSales.value = response.data
    } else {
      console.error('🧾 ❌ Error al cargar transacciones:', response.error)
      // Fallback: mantener array vacío en lugar de datos ficticios
      recentSales.value = []
    }
  } catch (error) {
    console.error('🧾 ❌ Error inesperado al cargar transacciones:', error)
    recentSales.value = []
  }
}

// Computed para compatibilidad (ahora apunta a la variable reactiva)
const recentSalesComputed = computed(() => recentSales.value)

// Funciones para alertas automáticas del inventario
const loadAndShowInventoryAlerts = async () => {
  try {
    // Obtener token de autenticación
    const token = authService.getToken()
    if (!token) {
      console.warn('Token no disponible para cargar alertas de inventario')
      return
    }
    
    // Cargar alertas y predicciones en paralelo
    const [alertsData, predictionsData] = await Promise.all([
      apiCall('/inventory/test/alerts?period=month'),
      apiCall('/inventory/test/predictions?period=month')
    ])
    
    const notificationsToShow = []
    
    // 1. Agregar 2 alertas críticas de stock
    if (alertsData.success && alertsData.data.alerts?.length > 0) {
      const criticalAlerts = alertsData.data.alerts
        .filter(alert => alert.severity === 'critical')
        .slice(0, 2)
        .map(alert => ({
          ...alert,
          type: 'error', // Para el componente de toast
          autoClose: true,
          duration: 15000
        }))
      notificationsToShow.push(...criticalAlerts)
    }
    
    // 2. Agregar 2 recomendaciones de compra prioritarias
    if (predictionsData.success && predictionsData.data.purchase_recommendations?.length > 0) {
      const topRecommendations = predictionsData.data.purchase_recommendations
        .filter(rec => rec.priority === 'critical' && rec.recommended_purchase > 0)
        .sort((a, b) => b.estimated_cost - a.estimated_cost) // Ordenar por costo estimado
        .slice(0, 2)
        .map(rec => ({
          id: `purchase_rec_${rec.product_id}`,
          alert_key: `purchase_rec_${rec.product_id}`,
          title: 'Recomendación de Compra',
          message: `Se recomienda comprar ${rec.recommended_purchase} unidades de '${rec.product_name}' por demanda proyectada`,
          type: 'warning',
          category: 'purchase_recommendation',
          product_id: rec.product_id,
          product_name: rec.product_name,
          recommended_quantity: rec.recommended_purchase,
          estimated_cost: rec.estimated_cost,
          priority: rec.priority,
          daily_demand: rec.daily_demand,
          action_url: `/inventory/purchase/${rec.product_id}`,
          action_text: 'Ver Recomendación',
          autoClose: true,
          duration: 18000,
          created_at: new Date().toISOString()
        }))
      notificationsToShow.push(...topRecommendations)
    }
    
    // 3. Agregar 1 alerta de tendencia si hay datos disponibles
    if (predictionsData.success && predictionsData.data.trend_analysis) {
      const trends = predictionsData.data.trend_analysis
      if (trends.sales?.growth_percentage !== 0 || trends.transactions?.current > 50) {
        notificationsToShow.push({
          id: 'trend_analysis_1',
          alert_key: 'trend_analysis_1',
          title: 'Análisis de Tendencias',
          message: `Has procesado ${trends.transactions.current} transacciones con un ticket promedio de $${(trends.average_ticket.current / 1000).toFixed(0)}k. ¡Buen rendimiento!`,
          type: 'info',
          category: 'trend_analysis',
          sales_data: trends,
          action_url: '/inventory/analytics',
          action_text: 'Ver Análisis',
          autoClose: true,
          duration: 12000,
          created_at: new Date().toISOString()
        })
      }
    }
    
    // Mostrar las notificaciones con espaciado profesional
    notificationsToShow.forEach((notification, index) => {
      setTimeout(() => {
        if (toastRef.value) {
          toastRef.value.show(notification)
        }
      }, (index + 1) * 3000) // Espaciar cada 3 segundos
    })
    
  } catch (err) {
    console.error('Error cargando alertas e información del inventario:', err)
  }
}

const handleAlertAction = (alert) => {
  // Debug logs removed for production
  
  if (alert.category === 'purchase_recommendation') {
    // Debug logs removed for production
    // Aquí podrías abrir un modal de compra o navegar al módulo de compras
  } else if (alert.category === 'trend_analysis') {
    // Debug logs removed for production
    // Aquí podrías navegar al módulo de analytics/estadísticas
  } else if (alert.category === 'stock') {
    // Debug logs removed for production
    // Aquí podrías navegar al módulo de inventario específico del producto
  }
  
  if (alert.action_url) {
    // Debug logs removed for production
    // Aquí implementarías la navegación real
  }
}

const handleDismissForever = async (alert) => {
  try {
    // Solo las alertas del backend se pueden descartar permanentemente
    if (alert.category === 'stock') {
      // Obtener token de autenticación
      const token = authService.getToken()
      if (!token) {
        console.warn('Token no disponible para descartar alerta')
        return
      }
      
      const data = await apiCall('/inventory/test/alerts/dismiss', {
        method: 'POST',
        body: JSON.stringify({
          alert_key: alert.alert_key || alert.id,
          alert_type: alert.category,
          product_id: alert.product_id,
          user_id: 1 // Default user para pruebas
        })
      })
      
      if (data.success) {
        // Debug logs removed for production
        showSuccessMessage('Alerta de stock descartada', 'No volverás a ver esta alerta de stock')
      }
    } else {
      // Para recomendaciones y análisis de tendencias, solo las ocultamos localmente
      // Debug logs removed for production
      showSuccessMessage('Notificación descartada', 'Esta notificación no se mostrará por un tiempo')
    }
    
  } catch (err) {
    console.error('Error descartando notificación desde dashboard:', err)
  }
}

const showSuccessMessage = (title, message) => {
  if (toastRef.value) {
    toastRef.value.show({
      title,
      message,
      type: 'success',
      autoClose: true,
      duration: 2000
    })
  }
}

// Cargar datos al montar el componente
onMounted(async () => {
  // Inicializar sesión de caja
  await loadCurrentSession()
  
  // Cargar datos del dashboard con período inicial
  await loadDashboardData(selectedPeriod.value)
  
  // Cargar sesiones activas
  await loadActiveSessions()
  
  // Cargar alertas de inventario después de que los datos se hayan cargado
  setTimeout(() => {
    loadAndShowInventoryAlerts()
  }, 2000)
  
  // 🎓 Iniciar tour si es primera visita
  if (isFirstVisitDashboard.value && dashboardTourRef.value) {
    setTimeout(() => {
      dashboardTourRef.value.startTour()
    }, 1500) // Esperar 1.5s para que todo esté cargado y visible
  }
})

// Watch para cambios en el período seleccionado
watch(selectedPeriod, async (newPeriod) => {
  await loadDashboardData(newPeriod)
})

// Función para cargar sesiones activas
const loadActiveSessions = async () => {
  try {
    const response = await apiCall('/cash-sessions')
    if (response.success) {
      // Filtrar solo las sesiones que están realmente abiertas
      activeSessions.value = response.sessions?.filter(session => session.status === 'open') || []
    }
  } catch (error) {
    console.error('Error loading active sessions:', error)
    activeSessions.value = []
  }
}

// Función para refrescar datos
const refreshData = async () => {
  await Promise.all([
    loadDashboardData(selectedPeriod.value),
    loadActiveSessions()
  ])
}

// Función para cambiar período
const changePeriod = (period) => {
  selectedPeriod.value = period
}

// Datos dinámicos basados en la API
const maxTopRevenue = computed(() => {
  const revenues = topProductsComputed.value.map(p => p.revenue)
  return revenues.length > 0 ? Math.max(...revenues) : 1
})

const formatDateTime = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit', day: '2-digit', month: '2-digit' })
}

// 🏦 Funciones para manejo de caja
const formatCurrency = (amount) => {
  if (!amount && amount !== 0) return '0'
  return new Intl.NumberFormat('es-CO', { 
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(amount)
}

const handleOpenCash = () => {
  console.log('🏦 handleOpenCash llamado desde DashboardView')
  // Redirigir al POS con instrucción para abrir modal de abrir caja
  emit('change-module', 'pos', { action: 'open-open-cash-modal' })
}

const handleCloseCash = () => {
  console.log('🏦 handleCloseCash llamado desde DashboardView')
  // Redirigir al POS con instrucción para abrir modal de cerrar caja
  emit('change-module', 'pos', { action: 'open-close-cash-modal' })
  console.log('🏦 Evento change-module emitido con acción: open-close-cash-modal')
  
  // Mostrar mensaje informativo
  setTimeout(() => {
    showSuccessMessage('Redirigiendo', 'Accediendo al Punto de Venta para cerrar la caja')
  }, 100)
}

const showErrorMessage = (title, message) => {
  if (toastRef.value) {
    toastRef.value.show({
      title,
      message,
      type: 'error',
      autoClose: true,
      duration: 3000
    })
  }
}

// --- CONFIGURACIÓN DE CHART.JS (Versión Profesional con área bajo la curva) ---
const lineChartData = computed(() => ({
  labels: weeklyDataComputed.value.map(d => d.name),
  datasets: [
    {
      label: 'Ventas ($)',
      data: weeklyDataComputed.value.map(d => d.sales),
      borderColor: '#4f46e5', // indigo-600
      backgroundColor: 'rgba(79, 70, 229, 0.15)', // Área bajo la curva más visible
      borderWidth: 3,
      tension: 0.4, // Curva suave
      fill: true, // Habilitar área bajo la curva
      pointRadius: 6,
      pointHoverRadius: 8,
      pointBackgroundColor: '#4f46e5',
      pointBorderColor: '#fff',
      pointBorderWidth: 2,
      pointHoverBackgroundColor: '#4f46e5',
      pointHoverBorderColor: '#fff',
      pointHoverBorderWidth: 3,
    }
  ]
}))

const lineChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  interaction: {
    intersect: false,
    mode: 'index'
  },
  plugins: {
    legend: { display: false },
    filler: {
      propagate: false
    },
    tooltip: { 
      backgroundColor: '#1f2937', // gris oscuro
      titleColor: '#fff',
      bodyColor: '#fff',
      cornerRadius: 8,
      displayColors: false,
      padding: 12,
      titleFont: {
        size: 14,
        weight: 'bold'
      },
      bodyFont: {
        size: 13
      },
      callbacks: {
        label: (context) => `Ventas: $${context.parsed.y.toLocaleString('es-ES', { minimumFractionDigits: 0 })}`,
        title: (context) => `${context[0].label} - Últimas 24h`
      }
    }
  },
  scales: {
    x: { 
      grid: { 
        display: false 
      },
      ticks: { 
        color: '#6b7280',
        font: {
          size: 12,
          weight: '500'
        }
      }
    },
    y: { 
      grid: { 
        color: '#e5e7eb', 
        borderDash: [5, 5] 
      },
      ticks: { 
        color: '#6b7280',
        font: {
          size: 11
        },
        callback: (value) => `$${(value / 1000).toFixed(0)}k` // Formato en K
      },
      beginAtZero: true
    }
  },
  elements: {
    line: {
      borderJoinStyle: 'round'
    }
  }
}

// Colores para el gráfico circular
const doughnutColors = [
  '#10b981', // emerald-500
  '#3b82f6', // blue-500
  '#f59e0b', // amber-500
  '#ef4444', // red-500
  '#8b5cf6'  // violet-500
]

// Datos para gráfico circular (Doughnut) - Solo top 4
const doughnutChartData = computed(() => ({
  labels: topProductsComputed.value.slice(0, 4).map(p => p.name),
  datasets: [{
    data: topProductsComputed.value.slice(0, 4).map(p => p.revenue),
    backgroundColor: doughnutColors.slice(0, 4),
    borderColor: '#ffffff',
    borderWidth: 2,
    hoverOffset: 10
  }]
}))

// Opciones para gráfico circular
const doughnutChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: false // Ocultamos la leyenda por defecto y usamos nuestra propia
    },
    tooltip: {
      backgroundColor: '#1f2937',
      titleColor: '#fff',
      bodyColor: '#fff',
      cornerRadius: 8,
      padding: 12,
      callbacks: {
        label: (context) => {
          const value = context.parsed
          const total = context.dataset.data.reduce((a, b) => a + b, 0)
          const percentage = ((value / total) * 100).toFixed(1)
          // Formato con puntos de miles
          const formattedValue = value.toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 })
          return `$${formattedValue} (${percentage}%)`
        }
      }
    }
  },
  cutout: '65%' // Tamaño del agujero central
}

const emit = defineEmits(['change-module'])
const newSale = () => {
  emit('change-module', 'pos')
}

const goToCashAdmin = () => {
  emit('change-module', 'cash-admin')
}
</script>

<style scoped>
@keyframes fade-in {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
  animation: fade-in 0.6s ease-out;
}

/* Transiciones suaves globales */
* {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>