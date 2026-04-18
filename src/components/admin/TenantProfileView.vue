<template>
  <div class="space-y-5 animate-fade-in">
    <!-- Back + Header -->
    <div class="flex items-center gap-4">
      <button @click="$emit('back')" class="p-2 text-gray-400 dark:text-zinc-500 hover:text-gray-700 dark:hover:text-zinc-200 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-xl transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
      </button>
      <div class="flex items-center gap-3 flex-1 min-w-0">
        <div class="w-11 h-11 rounded-xl bg-gray-100 dark:bg-zinc-800 flex items-center justify-center flex-shrink-0 border border-gray-200 dark:border-zinc-700">
          <span class="text-base font-bold text-gray-500 dark:text-zinc-400">{{ initial }}</span>
        </div>
        <div class="min-w-0">
          <h2 class="text-lg font-bold text-gray-900 dark:text-white truncate">{{ tenant.business_name || tenant.name }}</h2>
          <div class="flex items-center gap-2 mt-0.5">
            <span class="text-xs text-gray-400 dark:text-zinc-500 font-mono truncate">{{ tenant.primary_domain || tenant.domain }}</span>
            <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-zinc-600"></span>
            <span :class="statusClasses" class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold border">
              <span :class="statusDot" class="w-1.5 h-1.5 rounded-full"></span>
              {{ statusLabel }}
            </span>
            <span :class="planBadgeClasses" class="px-2 py-0.5 rounded text-[10px] font-bold border">
              {{ (tenant.plan || 'N/A').replace('_', ' ').toUpperCase() }}
            </span>
          </div>
        </div>
      </div>
      <div class="flex items-center gap-2 flex-shrink-0">
        <a :href="'https://' + (tenant.primary_domain || tenant.domain) + '/login'" target="_blank" class="px-4 py-2 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 text-sm font-medium rounded-xl border border-gray-200 dark:border-zinc-700 transition-all flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
          </svg>
          Entrar
        </a>
        <button @click="toggleStatus" class="px-4 py-2 text-sm font-medium rounded-xl border transition-all flex items-center gap-2" :class="tenant.status === 'active' ? 'bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-900 hover:bg-amber-100 dark:hover:bg-amber-900/40' : 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-900 hover:bg-emerald-100 dark:hover:bg-emerald-900/40'">
          <svg v-if="tenant.status === 'active'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/></svg>
          {{ tenant.status === 'active' ? 'Suspender' : 'Activar' }}
        </button>
        <button @click="showDeleteConfirm = true" class="px-4 py-2 bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-400 text-sm font-medium rounded-xl border border-rose-200 dark:border-rose-900 hover:bg-rose-100 dark:hover:bg-rose-900/40 transition-all flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
          Eliminar
        </button>
      </div>
    </div>

    <!-- Tabs -->
    <div class="border-b border-gray-200 dark:border-zinc-800">
      <div class="flex gap-1">
        <button v-for="tab in tabs" :key="tab.key" @click="activeTab = tab.key" :class="activeTab === tab.key ? 'border-blue-500 text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-950/20' : 'border-transparent text-gray-500 dark:text-zinc-500 hover:text-gray-700 dark:hover:text-zinc-300 hover:bg-gray-50 dark:hover:bg-zinc-800/50'" class="px-4 py-2.5 border-b-2 text-sm font-medium transition-all rounded-t-lg flex items-center gap-2">
          <component :is="tab.icon" class="w-4 h-4" />
          {{ tab.label }}
        </button>
      </div>
    </div>

    <!-- TAB: Resumen -->
    <div v-if="activeTab === 'overview'" class="space-y-5">
      <!-- Info Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- Información General -->
        <div class="lg:col-span-2 bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 p-5">
          <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Información General</h3>
          <div class="grid grid-cols-2 gap-x-8 gap-y-4">
            <div>
              <p class="text-xs text-gray-400 dark:text-zinc-500 mb-0.5">Propietario</p>
              <p class="text-sm font-medium text-gray-900 dark:text-white">{{ tenant.owner_name || 'No registrado' }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-400 dark:text-zinc-500 mb-0.5">Cédula / NIT</p>
              <p class="text-sm font-medium text-gray-900 dark:text-white">{{ tenant.cedula || 'No registrado' }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-400 dark:text-zinc-500 mb-0.5">Email Administrador</p>
              <p class="text-sm font-medium text-gray-900 dark:text-white">{{ tenant.admin_email || 'No registrado' }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-400 dark:text-zinc-500 mb-0.5">Teléfono</p>
              <p class="text-sm font-medium text-gray-900 dark:text-white">{{ tenant.admin_phone || 'No registrado' }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-400 dark:text-zinc-500 mb-0.5">Dominio</p>
              <a :href="'https://' + (tenant.primary_domain || tenant.domain)" target="_blank" class="text-sm text-blue-600 dark:text-blue-400 hover:underline font-mono">{{ tenant.primary_domain || tenant.domain }}</a>
            </div>
            <div>
              <p class="text-xs text-gray-400 dark:text-zinc-500 mb-0.5">ID Tenant</p>
              <p class="text-sm font-mono text-gray-600 dark:text-zinc-400">{{ tenant.id }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-400 dark:text-zinc-500 mb-0.5">Fecha de Registro</p>
              <p class="text-sm font-medium text-gray-900 dark:text-white">{{ formatDate(tenant.created_at) }}</p>
            </div>
          </div>
        </div>

        <!-- Suscripción -->
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 p-5">
          <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Suscripción</h3>
          <div class="space-y-3">
            <div class="flex items-center justify-between">
              <span class="text-xs text-gray-400 dark:text-zinc-500">Plan</span>
              <span :class="planBadgeClasses" class="px-2 py-0.5 rounded text-[10px] font-bold border">
                {{ (tenant.plan || 'N/A').replace('_', ' ').toUpperCase() }}
              </span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-xs text-gray-400 dark:text-zinc-500">Inicio</span>
              <span class="text-sm font-medium text-gray-900 dark:text-white">{{ tenant.subscription_start ? formatDate(tenant.subscription_start) : '—' }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-xs text-gray-400 dark:text-zinc-500">Vencimiento</span>
              <span class="text-sm font-medium" :class="isExpired ? 'text-rose-600 dark:text-rose-400' : isExpiringSoon ? 'text-amber-600 dark:text-amber-400' : 'text-gray-900 dark:text-white'">
                {{ tenant.subscription_end ? formatDate(tenant.subscription_end) : '—' }}
              </span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-xs text-gray-400 dark:text-zinc-500">Días restantes</span>
              <span class="text-sm font-bold" :class="isExpired ? 'text-rose-600 dark:text-rose-400' : daysRemaining <= 7 ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400'">
                {{ daysRemaining !== null ? (daysRemaining < 0 ? 'Expirado' : daysRemaining + ' días') : '—' }}
              </span>
            </div>
            <div class="pt-2 border-t border-gray-100 dark:border-zinc-800">
              <button @click="activeTab = 'subscription'" class="w-full text-center text-xs text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium py-1.5 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-950/30 transition-colors">
                Gestionar suscripción →
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Métricas -->
      <div v-if="hasStats" class="grid grid-cols-2 md:grid-cols-5 gap-3">
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 p-4 text-center">
          <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ formatNumber(tenant.stats?.total_users) }}</p>
          <p class="text-xs text-gray-400 dark:text-zinc-500 mt-1">Usuarios</p>
        </div>
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 p-4 text-center">
          <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ formatNumber(tenant.stats?.total_products) }}</p>
          <p class="text-xs text-gray-400 dark:text-zinc-500 mt-1">Productos</p>
        </div>
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 p-4 text-center">
          <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ formatNumber(tenant.stats?.total_sales) }}</p>
          <p class="text-xs text-gray-400 dark:text-zinc-500 mt-1">Ventas</p>
        </div>
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 p-4 text-center">
          <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ formatNumber(tenant.stats?.total_customers) }}</p>
          <p class="text-xs text-gray-400 dark:text-zinc-500 mt-1">Clientes</p>
        </div>
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 p-4 text-center">
          <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">${{ formatCurrency(tenant.stats?.total_revenue) }}</p>
          <p class="text-xs text-gray-400 dark:text-zinc-500 mt-1">Ingresos</p>
        </div>
      </div>
      <div v-else class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 p-8 text-center">
        <p class="text-sm text-gray-400 dark:text-zinc-500">No se pudieron cargar las métricas de esta tienda</p>
      </div>
    </div>

    <!-- TAB: Usuarios -->
    <div v-if="activeTab === 'users'" class="space-y-4">
      <div class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-zinc-800 flex items-center justify-between">
          <div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Usuarios de la Tienda</h3>
            <p class="text-xs text-gray-400 dark:text-zinc-500 mt-0.5">{{ users.length }} usuarios registrados</p>
          </div>
          <button @click="loadUsers" class="p-2 text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-lg transition-all" :class="{'animate-spin': loadingUsers}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
          </button>
        </div>

        <div v-if="loadingUsers" class="p-8 text-center">
          <svg class="w-6 h-6 animate-spin mx-auto text-blue-500 mb-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
          <p class="text-sm text-gray-400 dark:text-zinc-500">Cargando usuarios...</p>
        </div>

        <div v-else-if="users.length === 0" class="p-10 text-center">
          <svg class="w-10 h-10 mx-auto mb-2 text-gray-200 dark:text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
          <p class="text-sm text-gray-400 dark:text-zinc-500">No hay usuarios registrados</p>
        </div>

        <div v-else class="divide-y divide-gray-50 dark:divide-zinc-800/50">
          <div v-for="user in users" :key="user.id" class="px-5 py-3.5 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-zinc-800/30 transition-colors">
            <div class="flex items-center gap-3 min-w-0">
              <div class="w-9 h-9 rounded-lg bg-purple-100 dark:bg-purple-950/50 flex items-center justify-center flex-shrink-0">
                <span class="text-sm font-bold text-purple-600 dark:text-purple-400">{{ user.name.charAt(0).toUpperCase() }}</span>
              </div>
              <div class="min-w-0">
                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ user.name }}</p>
                <div class="flex items-center gap-2 mt-0.5">
                  <span class="text-xs text-gray-400 dark:text-zinc-500 truncate">{{ user.email }}</span>
                  <span v-if="user.role" class="px-1.5 py-0.5 bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400 text-[9px] font-bold rounded border border-blue-100 dark:border-blue-900">
                    {{ user.role.name || user.role }}
                  </span>
                </div>
              </div>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
              <button @click="resetPassword(user)" class="px-3 py-1.5 text-xs font-medium text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/30 hover:bg-amber-100 dark:hover:bg-amber-900/40 border border-amber-200 dark:border-amber-900 rounded-lg transition-all flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                Resetear
              </button>
              <span :class="user.active !== false ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800' : 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800'" class="px-2 py-0.5 text-[10px] font-bold rounded border">
                {{ user.active !== false ? 'Activo' : 'Inactivo' }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- TAB: Productos -->
    <div v-if="activeTab === 'products'" class="space-y-4">
      <div class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-zinc-800 flex items-center justify-between">
          <div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Productos de la Tienda</h3>
            <p class="text-xs text-gray-400 dark:text-zinc-500 mt-0.5">{{ products.length }} productos {{ productSearch ? '(filtrados)' : '' }}</p>
          </div>
          <div class="flex items-center gap-2">
            <div class="relative">
              <input v-model="productSearch" type="text" placeholder="Buscar producto..." class="pl-8 pr-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 placeholder-gray-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30 w-48">
              <svg class="w-3.5 h-3.5 text-gray-400 dark:text-zinc-500 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <button @click="loadProducts" class="p-2 text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-lg transition-all" :class="{'animate-spin': loadingProducts}">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </button>
          </div>
        </div>

        <div v-if="loadingProducts" class="p-8 text-center">
          <svg class="w-6 h-6 animate-spin mx-auto text-blue-500 mb-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
          <p class="text-sm text-gray-400 dark:text-zinc-500">Cargando productos...</p>
        </div>

        <div v-else-if="filteredProducts.length === 0" class="p-10 text-center">
          <svg class="w-10 h-10 mx-auto mb-2 text-gray-200 dark:text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
          <p class="text-sm text-gray-400 dark:text-zinc-500">{{ productSearch ? 'Sin resultados para esta búsqueda' : 'No hay productos registrados' }}</p>
        </div>

        <div v-else>
          <table class="w-full">
            <thead>
              <tr class="border-b border-gray-100 dark:border-zinc-800">
                <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Producto</th>
                <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Precio</th>
                <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Stock</th>
                <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Categoría</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-zinc-800/50">
              <tr v-for="product in paginatedProducts" :key="product.id" class="hover:bg-gray-50 dark:hover:bg-zinc-800/30 transition-colors">
                <td class="px-5 py-3">
                  <div class="flex items-center gap-3">
                    <div v-if="product.image_url" class="w-9 h-9 rounded-lg overflow-hidden flex-shrink-0 bg-gray-100 dark:bg-zinc-800">
                      <img :src="product.image_url" :alt="product.name" class="w-full h-full object-cover">
                    </div>
                    <div v-else class="w-9 h-9 rounded-lg bg-gray-100 dark:bg-zinc-800 flex items-center justify-center flex-shrink-0">
                      <svg class="w-4 h-4 text-gray-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <span class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ product.name }}</span>
                  </div>
                </td>
                <td class="px-5 py-3">
                  <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">${{ parseFloat(product.price || 0).toLocaleString() }}</span>
                </td>
                <td class="px-5 py-3">
                  <span class="text-sm" :class="(product.stock || 0) <= 5 ? 'text-rose-600 dark:text-rose-400 font-semibold' : 'text-gray-600 dark:text-zinc-400'">{{ product.stock || 0 }}</span>
                </td>
                <td class="px-5 py-3">
                  <span class="text-xs text-gray-400 dark:text-zinc-500">{{ product.category?.name || product.category_name || '—' }}</span>
                </td>
              </tr>
            </tbody>
          </table>
          <div v-if="totalProductPages > 1" class="px-5 py-3 border-t border-gray-100 dark:border-zinc-800 flex items-center justify-between">
            <p class="text-xs text-gray-400 dark:text-zinc-500">Mostrando {{ productPageStart + 1 }}-{{ Math.min(productPageStart + productsPerPage, filteredProducts.length) }} de {{ filteredProducts.length }}</p>
            <div class="flex items-center gap-1">
              <button @click="productPage = Math.max(0, productPage - 1)" :disabled="productPage === 0" class="px-2.5 py-1 text-xs font-medium text-gray-600 dark:text-zinc-400 bg-gray-50 dark:bg-zinc-800 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded-lg border border-gray-200 dark:border-zinc-700 disabled:opacity-40 transition-all">Ant</button>
              <span class="text-xs text-gray-400 dark:text-zinc-500 px-2">{{ productPage + 1 }} / {{ totalProductPages }}</span>
              <button @click="productPage = Math.min(totalProductPages - 1, productPage + 1)" :disabled="productPage >= totalProductPages - 1" class="px-2.5 py-1 text-xs font-medium text-gray-600 dark:text-zinc-400 bg-gray-50 dark:bg-zinc-800 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded-lg border border-gray-200 dark:border-zinc-700 disabled:opacity-40 transition-all">Sig</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- TAB: Suscripción -->
    <div v-if="activeTab === 'subscription'" class="space-y-5">
      
      <!-- Estado Actual - Card con grid limpio -->
      <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-gray-200 dark:border-zinc-800 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-zinc-800">
          <h3 class="text-sm font-bold text-gray-900 dark:text-white tracking-wide">Estado Actual</h3>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 divide-x divide-gray-100 dark:divide-zinc-800">
          <div class="px-6 py-5">
            <p class="text-[11px] font-medium text-gray-400 dark:text-zinc-500 uppercase tracking-wider mb-2">Plan</p>
            <span :class="planBadgeClasses" class="inline-flex px-2.5 py-1 rounded-lg text-xs font-bold border">
              {{ (tenant.plan || 'N/A').replace('_', ' ').toUpperCase() }}
            </span>
          </div>
          <div class="px-6 py-5">
            <p class="text-[11px] font-medium text-gray-400 dark:text-zinc-500 uppercase tracking-wider mb-2">Estado</p>
            <div class="flex items-center gap-2">
              <span :class="statusDot" class="w-2.5 h-2.5 rounded-full"></span>
              <span class="text-sm font-bold text-gray-900 dark:text-white">{{ statusLabel }}</span>
            </div>
          </div>
          <div class="px-6 py-5">
            <p class="text-[11px] font-medium text-gray-400 dark:text-zinc-500 uppercase tracking-wider mb-2">Inicio</p>
            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ tenant.subscription_start ? formatDate(tenant.subscription_start) : '—' }}</p>
          </div>
          <div class="px-6 py-5">
            <p class="text-[11px] font-medium text-gray-400 dark:text-zinc-500 uppercase tracking-wider mb-2">Vencimiento</p>
            <p class="text-sm font-bold" :class="isExpired ? 'text-rose-600 dark:text-rose-400' : isExpiringSoon ? 'text-amber-600 dark:text-amber-400' : 'text-gray-900 dark:text-white'">
              {{ tenant.subscription_end ? formatDate(tenant.subscription_end) : '—' }}
            </p>
            <p v-if="daysRemaining !== null" class="text-[11px] font-semibold mt-1" :class="isExpired ? 'text-rose-500' : daysRemaining <= 7 ? 'text-amber-500' : 'text-emerald-500'">
              {{ isExpired ? 'Expirado' : daysRemaining + ' días restantes' }}
            </p>
          </div>
        </div>
      </div>

      <!-- Cambiar Plan - Cards seleccionables -->
      <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-gray-200 dark:border-zinc-800 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-zinc-800">
          <h3 class="text-sm font-bold text-gray-900 dark:text-white tracking-wide">Cambiar Plan</h3>
        </div>
        <div class="p-5">
          <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <button v-for="p in plans" :key="p.key" @click="changePlan(p.key)" 
                    :class="[
                      'relative p-4 rounded-xl border-2 text-left transition-all duration-200 group',
                      tenant.plan === p.key 
                        ? 'border-blue-500 dark:border-blue-400 bg-blue-50/80 dark:bg-blue-950/30 shadow-sm shadow-blue-500/10' 
                        : 'border-gray-200 dark:border-zinc-700 hover:border-gray-300 dark:hover:border-zinc-600 hover:shadow-sm'
                    ]">
              <div v-if="tenant.plan === p.key" class="absolute top-2.5 right-2.5">
                <svg class="w-5 h-5 text-blue-500 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
              </div>
              <p class="text-sm font-bold text-gray-900 dark:text-white mb-0.5">{{ p.label }}</p>
              <p class="text-xs text-gray-400 dark:text-zinc-500">{{ p.price }}</p>
            </button>
          </div>
        </div>
      </div>

      <!-- Límites de la Tienda -->
      <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-gray-200 dark:border-zinc-800 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-zinc-800 flex items-center justify-between">
          <div>
            <h3 class="text-sm font-bold text-gray-900 dark:text-white tracking-wide">Límites de la Tienda</h3>
            <p class="text-[11px] text-gray-400 dark:text-zinc-500 mt-0.5">Configura el máximo de usuarios y sedes permitidas</p>
          </div>
          <div v-if="limitsChanged" class="flex items-center gap-2">
            <span class="text-[11px] text-amber-600 dark:text-amber-400 font-medium">Sin guardar</span>
            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
          </div>
        </div>
        <div class="p-5">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- Max Usuarios -->
            <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-5 border border-gray-100 dark:border-zinc-700/50">
              <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/50 border border-blue-100 dark:border-blue-900/50 flex items-center justify-center">
                  <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                  </svg>
                </div>
                <div>
                  <p class="text-sm font-bold text-gray-900 dark:text-white">Máx. Usuarios</p>
                  <p class="text-[11px] text-gray-400 dark:text-zinc-500">Usuarios que pueden registrarse</p>
                </div>
              </div>
              <div class="flex items-center gap-3">
                <input type="number" v-model.number="editLimits.max_users" min="1" max="999" placeholder="Sin límite"
                       class="flex-1 px-4 py-2.5 text-sm font-bold text-center bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                <div class="flex gap-1">
                  <button v-for="preset in [3, 5, 10, 20]" :key="preset" @click="editLimits.max_users = preset"
                          :class="editLimits.max_users === preset ? 'bg-blue-600 text-white border-blue-600 dark:border-blue-500' : 'bg-white dark:bg-zinc-800 text-gray-600 dark:text-zinc-400 border-gray-200 dark:border-zinc-700 hover:border-gray-300 dark:hover:border-zinc-600'"
                          class="w-9 h-9 flex items-center justify-center text-xs font-bold rounded-lg border transition-all">
                    {{ preset }}
                  </button>
                </div>
              </div>
              <p v-if="tenant.stats?.total_users" class="text-[11px] text-gray-400 dark:text-zinc-500 mt-3">
                Actualmente: <strong class="text-gray-700 dark:text-zinc-300">{{ tenant.stats.total_users }}</strong> usuarios registrados
              </p>
            </div>

            <!-- Max Sedes -->
            <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-5 border border-gray-100 dark:border-zinc-700/50">
              <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950/50 border border-purple-100 dark:border-purple-900/50 flex items-center justify-center">
                  <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.15c0 .415.336.75.75.75z"/>
                  </svg>
                </div>
                <div>
                  <p class="text-sm font-bold text-gray-900 dark:text-white">Máx. Sedes</p>
                  <p class="text-[11px] text-gray-400 dark:text-zinc-500">Bodegas/sucursales permitidas</p>
                </div>
              </div>
              <div class="flex items-center gap-3">
                <input type="number" v-model.number="editLimits.max_warehouses" min="1" max="99" placeholder="Sin límite"
                       class="flex-1 px-4 py-2.5 text-sm font-bold text-center bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                <div class="flex gap-1">
                  <button v-for="preset in [1, 3, 5, 10]" :key="preset" @click="editLimits.max_warehouses = preset"
                          :class="editLimits.max_warehouses === preset ? 'bg-purple-600 text-white border-purple-600 dark:border-purple-500' : 'bg-white dark:bg-zinc-800 text-gray-600 dark:text-zinc-400 border-gray-200 dark:border-zinc-700 hover:border-gray-300 dark:hover:border-zinc-600'"
                          class="w-9 h-9 flex items-center justify-center text-xs font-bold rounded-lg border transition-all">
                    {{ preset }}
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Botón guardar límites -->
          <button @click="saveLimits" :disabled="savingLimits || !limitsChanged"
                  :class="limitsChanged ? 'bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 shadow-lg shadow-slate-400/20 dark:shadow-slate-900/50' : 'bg-gray-200 dark:bg-zinc-800 cursor-not-allowed'"
                  class="w-full mt-5 py-3 text-white font-bold text-sm rounded-xl transition-all duration-300 flex items-center justify-center gap-2 disabled:opacity-60">
            <svg v-if="savingLimits" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            {{ savingLimits ? 'Guardando...' : 'Guardar Límites' }}
          </button>
        </div>
      </div>

      <!-- Modificar Fechas -->
      <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-gray-200 dark:border-zinc-800 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-zinc-800 flex items-center gap-2">
          <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
          </svg>
          <h3 class="text-sm font-bold text-gray-900 dark:text-white tracking-wide">Modificar Fechas del Plan</h3>
        </div>
        <div class="p-5">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
              <label class="block text-[11px] font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-2">Fecha de Inicio</label>
              <input type="date" v-model="editSubscription.start" 
                     class="w-full px-4 py-2.5 text-sm border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
            </div>
            <div>
              <label class="block text-[11px] font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-2">Fecha de Vencimiento</label>
              <input type="date" v-model="editSubscription.end" 
                     class="w-full px-4 py-2.5 text-sm border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
            </div>
          </div>
          <div class="flex flex-wrap gap-2 mb-5">
            <button v-for="ext in quickExtensions" :key="ext.days" @click="extendDays(ext.days)" 
                    class="px-3.5 py-2 bg-gray-50 dark:bg-zinc-800 text-gray-600 dark:text-zinc-400 text-xs font-semibold rounded-lg border border-gray-200 dark:border-zinc-700 hover:bg-gray-100 dark:hover:bg-zinc-700 hover:border-gray-300 dark:hover:border-zinc-600 transition-all">
              {{ ext.label }}
            </button>
          </div>
          <button @click="updateSubscriptionDates" :disabled="savingDates" 
                  class="w-full py-3 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white font-bold text-sm rounded-xl transition-all duration-200 disabled:opacity-50 flex items-center justify-center gap-2 shadow-sm shadow-blue-500/20">
            <svg v-if="savingDates" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            {{ savingDates ? 'Guardando...' : 'Guardar Fechas' }}
          </button>
        </div>
      </div>

      <!-- Pausar / Activar -->
      <div class="bg-white dark:bg-zinc-900 rounded-2xl border shadow-sm overflow-hidden"
           :class="tenant.status === 'active' ? 'border-amber-200 dark:border-amber-900/50' : 'border-emerald-200 dark:border-emerald-900/50'">
        <div class="px-6 py-4 border-b" :class="tenant.status === 'active' ? 'border-amber-100 dark:border-amber-900/30 bg-amber-50/50 dark:bg-amber-950/20' : 'border-emerald-100 dark:border-emerald-900/30 bg-emerald-50/50 dark:bg-emerald-950/20'">
          <div class="flex items-center gap-2">
            <svg v-if="tenant.status === 'active'" class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.25 9v6m-4.5 0V9M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <svg v-else class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15.91 11.672a.375.375 0 010 .656l-5.603 3.113a.375.375 0 01-.557-.328V8.887c0-.286.307-.466.557-.327l5.603 3.112z"/></svg>
            <h3 class="text-sm font-bold" :class="tenant.status === 'active' ? 'text-amber-700 dark:text-amber-400' : 'text-emerald-700 dark:text-emerald-400'">
              {{ tenant.status === 'active' ? 'Pausar Tienda' : 'Activar Tienda' }}
            </h3>
          </div>
        </div>
        <div class="px-6 py-5">
          <p class="text-xs text-gray-500 dark:text-zinc-400 mb-4">
            {{ tenant.status === 'active' ? 'Pausar impedirá que los usuarios accedan a la tienda. Los datos se conservan intactos.' : 'Activar permitirá que los usuarios vuelvan a acceder normalmente.' }}
          </p>
          <button @click="toggleStatus" 
                  :class="tenant.status === 'active' ? 'bg-amber-500 hover:bg-amber-600 shadow-amber-500/20' : 'bg-emerald-500 hover:bg-emerald-600 shadow-emerald-500/20'" 
                  class="px-6 py-2.5 text-white font-bold text-sm rounded-xl transition-all duration-200 shadow-sm">
            {{ tenant.status === 'active' ? 'Pausar Tienda' : 'Activar Tienda' }}
          </button>
        </div>
      </div>
    </div>

    <!-- TAB: Errores -->
    <div v-if="activeTab === 'errors'" class="space-y-4">
      <div class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-zinc-800 flex items-center justify-between">
          <div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Logs del Sistema</h3>
            <p class="text-xs text-gray-400 dark:text-zinc-500 mt-0.5">{{ errors.filter(e => !e.resolved).length }} registros activos</p>
          </div>
          <div class="flex items-center gap-2">
            <label class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-zinc-400 cursor-pointer">
              <input type="checkbox" v-model="showResolvedErrors" @change="loadErrors" class="rounded border-gray-300 dark:border-zinc-600 text-blue-600 focus:ring-blue-500">
              Mostrar resueltos
            </label>
            <button @click="analyzeAllErrors" :disabled="analyzingAll" class="px-3 py-1.5 text-xs font-medium bg-purple-50 dark:bg-purple-950/30 text-purple-700 dark:text-purple-400 border border-purple-200 dark:border-purple-900 hover:bg-purple-100 dark:hover:bg-purple-900/40 rounded-lg transition-all flex items-center gap-1.5 disabled:opacity-50">
              <svg v-if="analyzingAll" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
              <svg v-else class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
              {{ analyzingAll ? 'Analizando...' : 'Analizar con IA' }}
            </button>
            <button @click="loadErrors" class="p-2 text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-lg transition-all">
              <svg class="w-4 h-4" :class="{'animate-spin': loadingErrors}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </button>
          </div>
        </div>

        <div v-if="loadingErrors" class="p-8 text-center">
          <svg class="w-6 h-6 animate-spin mx-auto text-blue-500 mb-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
          <p class="text-sm text-gray-400 dark:text-zinc-500">Cargando errores...</p>
        </div>

        <div v-else-if="errors.length === 0" class="p-10 text-center">
          <svg class="w-10 h-10 mx-auto mb-2 text-emerald-200 dark:text-emerald-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <p class="text-sm text-gray-400 dark:text-zinc-500">Sin registros</p>
        </div>

        <div v-else class="divide-y divide-gray-50 dark:divide-zinc-800/50">
          <div v-for="error in errors" :key="error.id" class="px-5 py-4 hover:bg-gray-50 dark:hover:bg-zinc-800/20 transition-colors" :class="{'opacity-50': error.resolved}">
            <div class="flex items-start justify-between gap-4">
              <div class="flex-1 min-w-0">
                <!-- Severity + Type -->
                <div class="flex items-center gap-2 mb-1.5">
                  <span :class="{
                    'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800': error.severity === 'critical',
                    'bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border-amber-100 dark:border-amber-800': error.severity === 'error',
                    'bg-gray-50 dark:bg-zinc-800 text-gray-600 dark:text-zinc-400 border-gray-200 dark:border-zinc-700': error.severity === 'warning',
                    'bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400 border-blue-100 dark:border-blue-800': error.severity === 'info'
                  }" class="px-1.5 py-0.5 text-[9px] font-bold uppercase border rounded">
                    {{ error.severity }}
                  </span>
                  <span class="text-xs font-mono text-gray-500 dark:text-zinc-500">{{ error.type }}</span>
                  <span v-if="error.occurrence_count > 1" class="px-1.5 py-0.5 bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400 text-[9px] font-bold rounded border border-blue-100 dark:border-blue-900">
                    ×{{ error.occurrence_count }}
                  </span>
                  <span v-if="error.resolved" class="px-1.5 py-0.5 bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 text-[9px] font-bold rounded border border-emerald-100 dark:border-emerald-800">
                    RESUELTO
                  </span>
                </div>

                <!-- Message -->
                <p class="text-sm text-gray-900 dark:text-white font-medium leading-snug line-clamp-2">{{ error.message }}</p>

                <!-- AI Summary -->
                <div v-if="error.ai_summary" class="mt-2 px-3 py-2 bg-purple-50 dark:bg-purple-950/30 border border-purple-100 dark:border-purple-900/50 rounded-lg">
                  <div class="flex items-center gap-1.5 mb-1">
                    <svg class="w-3 h-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span class="text-[9px] font-bold text-purple-600 dark:text-purple-400 uppercase">Análisis IA</span>
                  </div>
                  <p class="text-xs text-purple-800 dark:text-purple-300 leading-relaxed">{{ error.ai_summary }}</p>
                </div>

                <!-- File + Time -->
                <div class="flex items-center gap-3 mt-2 text-[11px] text-gray-400 dark:text-zinc-500">
                  <span v-if="error.file" class="font-mono truncate max-w-[300px]">{{ error.file }}:{{ error.line }}</span>
                  <span>Última vez: {{ formatRelativeTime(error.last_seen_at) }}</span>
                  <span v-if="error.first_seen_at !== error.last_seen_at">Primera vez: {{ formatRelativeTime(error.first_seen_at) }}</span>
                </div>
              </div>

              <!-- Actions -->
              <div class="flex items-center gap-1 flex-shrink-0">
                <button v-if="!error.ai_summary" @click="analyzeError(error)" :disabled="error._analyzing" class="p-1.5 text-gray-400 dark:text-zinc-500 hover:text-purple-600 dark:hover:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded-lg transition-all" title="Analizar con IA">
                  <svg v-if="error._analyzing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                  <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </button>
                <button v-if="!error.resolved" @click="resolveError(error)" class="p-1.5 text-gray-400 dark:text-zinc-500 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-lg transition-all" title="Marcar como resuelto">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Inline: Resetear Contraseña -->
    <div v-if="showResetPasswordForm" class="fixed inset-0 bg-black/40 dark:bg-black/60 flex items-center justify-center z-50 p-4" @click.self="showResetPasswordForm = false">
      <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 max-w-sm w-full border border-gray-200 dark:border-zinc-800 shadow-2xl">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-9 h-9 bg-amber-50 dark:bg-amber-950 rounded-lg flex items-center justify-center">
            <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
          </div>
          <div>
            <h4 class="text-sm font-bold text-gray-900 dark:text-white">Resetear Contraseña</h4>
            <p class="text-xs text-gray-400 dark:text-zinc-500">{{ selectedUser?.name }}</p>
          </div>
        </div>
        <input v-model="newPassword" type="text" placeholder="Nueva contraseña" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-white rounded-lg border border-gray-200 dark:border-zinc-700 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm mb-4">
        <div class="flex gap-2">
          <button @click="showResetPasswordForm = false; selectedUser = null; newPassword = ''" class="flex-1 px-4 py-2.5 bg-gray-100 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 rounded-lg font-medium text-sm transition-colors">Cancelar</button>
          <button @click="confirmResetPassword" class="flex-1 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-medium text-sm transition-colors">Confirmar</button>
        </div>
      </div>
    </div>

    <!-- Inline: Confirmar Eliminación -->
    <div v-if="showDeleteConfirm" class="fixed inset-0 bg-black/40 dark:bg-black/60 flex items-center justify-center z-50 p-4" @click.self="showDeleteConfirm = false">
      <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 max-w-sm w-full border border-gray-200 dark:border-zinc-800 shadow-2xl">
        <div class="flex items-center gap-3 mb-3">
          <div class="w-9 h-9 bg-rose-50 dark:bg-rose-950 rounded-lg flex items-center justify-center">
            <svg class="w-4 h-4 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
          </div>
          <div>
            <h4 class="text-sm font-bold text-gray-900 dark:text-white">Eliminar Tenant</h4>
            <p class="text-xs text-gray-400 dark:text-zinc-500">{{ tenant.business_name || tenant.name }}</p>
          </div>
        </div>
        <p class="text-sm text-gray-500 dark:text-zinc-400 mb-4">Esta acción eliminará permanentemente el tenant, sus datos y usuarios. No se puede deshacer.</p>
        <div class="flex gap-2">
          <button @click="showDeleteConfirm = false" class="flex-1 px-4 py-2.5 bg-gray-100 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 rounded-lg font-medium text-sm transition-colors">Cancelar</button>
          <button @click="deleteTenant" class="flex-1 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-lg font-medium text-sm transition-colors">Eliminar</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, h } from 'vue'
import axios from 'axios'
import { useToast } from '../../composables/useToast.js'

const { showError } = useToast()

const props = defineProps({
  tenant: { type: Object, required: true }
})

const emit = defineEmits(['back', 'update-plan', 'toggle-status', 'delete', 'refresh'])

// State
const activeTab = ref('overview')
const users = ref([])
const products = ref([])
const loadingUsers = ref(false)
const loadingProducts = ref(false)
const productSearch = ref('')
const productPage = ref(0)
const productsPerPage = 15
const showResetPasswordForm = ref(false)
const selectedUser = ref(null)
const newPassword = ref('')
const showDeleteConfirm = ref(false)
const savingDates = ref(false)
const errors = ref([])
const loadingErrors = ref(false)
const showResolvedErrors = ref(false)
const analyzingAll = ref(false)

const editSubscription = ref({ start: '', end: '' })
const editLimits = ref({ max_users: null, max_warehouses: null })
const savingLimits = ref(false)

// Tab icons as render functions
const IconOverview = { render: () => h('svg', { class: 'w-4 h-4', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z' })])}
const IconUsers = { render: () => h('svg', { class: 'w-4 h-4', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z' })])}
const IconProducts = { render: () => h('svg', { class: 'w-4 h-4', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' })])}
const IconSubscription = { render: () => h('svg', { class: 'w-4 h-4', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z' })])}
const IconErrors = { render: () => h('svg', { class: 'w-4 h-4', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z' })])}

const tabs = [
  { key: 'overview', label: 'Resumen', icon: IconOverview },
  { key: 'users', label: 'Usuarios', icon: IconUsers },
  { key: 'products', label: 'Productos', icon: IconProducts },
  { key: 'subscription', label: 'Suscripción', icon: IconSubscription },
  { key: 'errors', label: 'Logs', icon: IconErrors }
]

const plans = [
  { key: 'free_trial', label: 'Free Trial', price: 'Gratis' },
  { key: 'basic', label: 'Basic', price: '$49.900/mes' },
  { key: 'premium', label: 'Premium', price: '$89.900/mes' },
  { key: 'enterprise', label: 'Enterprise', price: 'Personalizado' }
]

const quickExtensions = [
  { days: 7, label: '+7 días' },
  { days: 15, label: '+15 días' },
  { days: 30, label: '+1 mes' },
  { days: 90, label: '+3 meses' },
  { days: 365, label: '+1 año' }
]

// Computed
const initial = computed(() => (props.tenant.business_name || props.tenant.name || 'N')[0].toUpperCase())

const planBadgeClasses = computed(() => {
  const badges = {
    'free_trial': 'bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 border-gray-200 dark:border-zinc-700',
    'basic': 'bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400 border-blue-100 dark:border-blue-800',
    'premium': 'bg-purple-50 dark:bg-purple-950 text-purple-700 dark:text-purple-400 border-purple-100 dark:border-purple-800',
    'enterprise': 'bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border-amber-100 dark:border-amber-800'
  }
  return badges[props.tenant.plan] || badges.free_trial
})

const statusClasses = computed(() => {
  const s = props.tenant.status
  if (s === 'active') return 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800'
  if (s === 'paused') return 'bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border-amber-100 dark:border-amber-800'
  return 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-800'
})

const statusDot = computed(() => {
  const s = props.tenant.status
  if (s === 'active') return 'bg-emerald-500'
  if (s === 'paused') return 'bg-amber-500'
  return 'bg-rose-500'
})

const statusLabel = computed(() => {
  const s = props.tenant.status
  if (s === 'active') return 'Activo'
  if (s === 'paused') return 'Pausado'
  return 'Suspendido'
})

const isExpired = computed(() => {
  if (!props.tenant.subscription_end) return false
  return new Date(props.tenant.subscription_end) < new Date()
})

const isExpiringSoon = computed(() => {
  if (!props.tenant.subscription_end || isExpired.value) return false
  const diff = (new Date(props.tenant.subscription_end) - new Date()) / (1000 * 60 * 60 * 24)
  return diff <= 7
})

const daysRemaining = computed(() => {
  if (!props.tenant.subscription_end) return null
  return Math.ceil((new Date(props.tenant.subscription_end) - new Date()) / (1000 * 60 * 60 * 24))
})

const hasStats = computed(() => {
  return props.tenant.stats && !props.tenant.stats.error
})

const limitsChanged = computed(() => {
  const origUsers = props.tenant.max_users ?? null
  const origWarehouses = props.tenant.max_warehouses ?? null
  return editLimits.value.max_users !== origUsers || editLimits.value.max_warehouses !== origWarehouses
})

const filteredProducts = computed(() => {
  if (!productSearch.value) return products.value
  const q = productSearch.value.toLowerCase()
  return products.value.filter(p => p.name.toLowerCase().includes(q))
})

const totalProductPages = computed(() => Math.ceil(filteredProducts.value.length / productsPerPage))
const productPageStart = computed(() => productPage.value * productsPerPage)
const paginatedProducts = computed(() => filteredProducts.value.slice(productPageStart.value, productPageStart.value + productsPerPage))

// Methods
const formatDate = (d) => {
  if (!d) return '—'
  return new Date(d).toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: 'numeric' })
}

const formatNumber = (n) => (n ?? 0).toLocaleString()
const formatCurrency = (n) => parseFloat(n || 0).toLocaleString()

const loadUsers = async () => {
  loadingUsers.value = true
  try {
    const res = await axios.get(`/admin/api/tenants/${props.tenant.id}/users`)
    if (res.data.success) users.value = res.data.data
  } catch { /* silently fail */ }
  loadingUsers.value = false
}

const loadProducts = async () => {
  loadingProducts.value = true
  try {
    const res = await axios.get(`/admin/api/tenants/${props.tenant.id}/products`)
    if (res.data.success) products.value = res.data.data
  } catch { /* silently fail */ }
  loadingProducts.value = false
}

const resetPassword = (user) => {
  selectedUser.value = user
  newPassword.value = ''
  showResetPasswordForm.value = true
}

const confirmResetPassword = async () => {
  if (!newPassword.value) return
  try {
    const res = await axios.post(`/admin/api/tenants/${props.tenant.id}/users/${selectedUser.value.id}/reset-password`, { password: newPassword.value })
    if (res.data.success) {
      showResetPasswordForm.value = false
      selectedUser.value = null
      newPassword.value = ''
    }
  } catch (error) {
    showError('Error: ' + (error.response?.data?.message || error.message))
  }
}

const changePlan = (plan) => {
  if (plan === props.tenant.plan) return
  emit('update-plan', props.tenant.id, plan)
}

const toggleStatus = () => {
  emit('toggle-status', props.tenant.id, props.tenant.status)
}

const deleteTenant = () => {
  showDeleteConfirm.value = false
  emit('delete', props.tenant)
}

const initDates = () => {
  if (props.tenant.subscription_start) editSubscription.value.start = props.tenant.subscription_start.split('T')[0]
  if (props.tenant.subscription_end) editSubscription.value.end = props.tenant.subscription_end.split('T')[0]
}

const extendDays = (days) => {
  let base = editSubscription.value.end ? new Date(editSubscription.value.end) : new Date()
  if (base < new Date()) base = new Date()
  base.setDate(base.getDate() + days)
  editSubscription.value.end = base.toISOString().split('T')[0]
}

const updateSubscriptionDates = async () => {
  if (!editSubscription.value.start || !editSubscription.value.end) return
  savingDates.value = true
  try {
    const res = await axios.put(`/api/admin/tenants/${props.tenant.id}/subscription`, {
      subscription_start: editSubscription.value.start,
      subscription_end: editSubscription.value.end
    })
    if (res.data.success) {
      emit('refresh')
    } else {
      showError('Error: ' + (res.data.message || 'No se pudo actualizar'))
    }
  } catch (error) {
    showError('Error: ' + (error.response?.data?.message || error.message))
  }
  savingDates.value = false
}

const saveLimits = async () => {
  savingLimits.value = true
  try {
    const res = await axios.put(`/admin/api/tenants/${props.tenant.id}`, {
      max_users: editLimits.value.max_users || null,
      max_warehouses: editLimits.value.max_warehouses || null
    })
    if (res.data.success) {
      emit('refresh')
    } else {
      showError('Error: ' + (res.data.message || 'No se pudo actualizar'))
    }
  } catch (error) {
    showError('Error: ' + (error.response?.data?.message || error.message))
  }
  savingLimits.value = false
}

const initLimits = () => {
  editLimits.value.max_users = props.tenant.max_users ?? null
  editLimits.value.max_warehouses = props.tenant.max_warehouses ?? null
}

const loadErrors = async () => {
  loadingErrors.value = true
  try {
    const res = await axios.get(`/api/admin/tenants/${props.tenant.id}/errors`, {
      params: { include_resolved: showResolvedErrors.value ? 1 : 0 }
    })
    if (res.data.success) errors.value = res.data.data.map(e => ({ ...e, _analyzing: false }))
  } catch { /* silently fail */ }
  loadingErrors.value = false
}

const analyzeError = async (error) => {
  error._analyzing = true
  try {
    const res = await axios.post(`/api/admin/tenants/${props.tenant.id}/errors/${error.id}/analyze`)
    if (res.data.success) error.ai_summary = res.data.data.ai_summary
  } catch { /* silently fail */ }
  error._analyzing = false
}

const resolveError = async (error) => {
  try {
    const res = await axios.post(`/api/admin/tenants/${props.tenant.id}/errors/${error.id}/resolve`)
    if (res.data.success) {
      error.resolved = true
      error.resolved_at = new Date().toISOString()
    }
  } catch { /* silently fail */ }
}

const analyzeAllErrors = async () => {
  analyzingAll.value = true
  try {
    const res = await axios.post(`/api/admin/tenants/${props.tenant.id}/errors/analyze-all`)
    if (res.data.success) await loadErrors()
  } catch { /* silently fail */ }
  analyzingAll.value = false
}

const formatRelativeTime = (dateStr) => {
  if (!dateStr) return '—'
  const diff = (Date.now() - new Date(dateStr).getTime()) / 1000
  if (diff < 60) return 'hace unos segundos'
  if (diff < 3600) return `hace ${Math.floor(diff / 60)} min`
  if (diff < 86400) return `hace ${Math.floor(diff / 3600)}h`
  if (diff < 604800) return `hace ${Math.floor(diff / 86400)}d`
  return new Date(dateStr).toLocaleDateString('es-ES', { day: 'numeric', month: 'short' })
}

onMounted(() => {
  loadUsers()
  loadProducts()
  loadErrors()
  initDates()
  initLimits()
})
</script>
