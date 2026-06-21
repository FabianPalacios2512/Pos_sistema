<template>
  <div class="flex flex-col h-full bg-white dark:bg-zinc-950 animate-fade-in relative">
    <!-- Header: MS365 Style -->
    <div class="px-8 py-6 shrink-0 relative">
      <button @click="$emit('back')" class="absolute top-4 right-4 p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-md transition-colors focus:outline-none">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>

      <div class="flex items-start gap-5">
        <div class="w-20 h-20 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0">
          <span class="text-3xl font-semibold text-white">{{ initial }}</span>
        </div>
        <div class="min-w-0 mt-1 space-y-2">
          <h2 class="text-2xl font-semibold text-gray-900 dark:text-white truncate">{{ tenant.business_name || tenant.name }}</h2>
          <div class="flex items-center gap-1.5 text-[13px]">
             <span :class="tenant.status === 'active' ? 'text-emerald-600' : 'text-rose-600'">{{ statusLabel }}</span>
             <span class="text-gray-400">•</span>
             <span class="text-gray-600 dark:text-zinc-400">{{ tenant.primary_domain || tenant.domain }}</span>
          </div>
          <div class="flex flex-wrap items-center gap-5 text-[13px] mt-4">
            <a :href="'https://' + (tenant.primary_domain || tenant.domain) + '/login'" target="_blank" class="text-blue-600 hover:underline flex items-center gap-1.5">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
              Entrar al panel
            </a>
            <button @click="toggleStatus" class="text-blue-600 hover:underline flex items-center gap-1.5">
              <svg v-if="tenant.status === 'active'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/></svg>
              {{ tenant.status === 'active' ? 'Suspender cuenta' : 'Activar cuenta' }}
            </button>
            <button v-if="!tenant.deleted_at" @click="showDeleteConfirm = true" class="text-gray-500 hover:underline flex items-center gap-1.5">
              Eliminar usuario
            </button>
            <template v-else>
              <button @click="$emit('restore', tenant.id)" class="text-emerald-600 hover:underline flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Restaurar usuario
              </button>
              <button @click="showDeleteConfirm = true" class="text-rose-600 hover:underline flex items-center gap-1.5">
                Eliminar permanentemente
              </button>
            </template>
          </div>
        </div>
      </div>
    </div>

    <!-- Contenido Scrollable -->
    <div class="flex-1 overflow-y-auto custom-scrollbar px-8 py-4 space-y-8 pb-20">
      <!-- Tabs -->
      <div class="border-b border-gray-200 dark:border-zinc-800 -mx-8 px-8 mb-8">
        <div class="flex gap-6 overflow-x-auto custom-scrollbar">
          <button v-for="tab in tabs" :key="tab.key" @click="activeTab = tab.key" :class="activeTab === tab.key ? 'border-gray-900 text-gray-900 dark:text-white dark:border-white font-semibold' : 'border-transparent text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-zinc-300'" class="pb-3 border-b-2 text-[13px] transition-colors whitespace-nowrap">
            {{ tab.label }}
          </button>
        </div>
      </div>

    <!-- TAB: Resumen -->
    <div v-if="activeTab === 'overview'" class="space-y-5">
      <!-- Métricas KPI - Estilo Plano MS365 -->
      <div v-if="hasStats" class="mb-10">
        <h3 class="text-[15px] font-semibold text-gray-900 dark:text-white mb-5">Resumen de métricas</h3>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-y-6 gap-x-10">
          <div>
            <p class="text-[13px] font-semibold text-gray-900 dark:text-white mb-1">Usuarios</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ formatNumber(tenant.stats?.total_users) }}</p>
          </div>
          <div>
            <p class="text-[13px] font-semibold text-gray-900 dark:text-white mb-1">Productos</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ formatNumber(tenant.stats?.total_products) }}</p>
          </div>
          <div>
            <p class="text-[13px] font-semibold text-gray-900 dark:text-white mb-1">Ventas</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ formatNumber(tenant.stats?.total_sales) }}</p>
          </div>
          <div>
            <p class="text-[13px] font-semibold text-gray-900 dark:text-white mb-1">Clientes</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ formatNumber(tenant.stats?.total_customers) }}</p>
          </div>
          <div>
            <p class="text-[13px] font-semibold text-gray-900 dark:text-white mb-1">Ingresos</p>
            <p class="text-2xl font-semibold text-emerald-600 dark:text-emerald-400">${{ formatCurrency(tenant.stats?.total_revenue) }}</p>
          </div>
        </div>
      </div>
      <div v-else class="mb-10">
        <p class="text-[13px] text-gray-600 dark:text-zinc-400">No se pudieron cargar las métricas de esta tienda</p>
      </div>
    </div>

    <!-- TAB: Usuarios MS365 Style -->
    <div v-if="activeTab === 'users'" class="mb-10">
      <div class="flex items-center justify-between mb-5">
        <h3 class="text-[15px] font-semibold text-gray-900 dark:text-white">Usuarios de la Tienda ({{ users.length }})</h3>
        <button @click="loadUsers" class="text-[13px] text-blue-600 hover:underline flex items-center gap-1.5" :class="{'opacity-50 cursor-not-allowed': loadingUsers}">
          <svg class="w-4 h-4" :class="{'animate-spin': loadingUsers}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
          </svg>
          Actualizar lista
        </button>
      </div>

      <div v-if="loadingUsers" class="py-8">
        <p class="text-[13px] text-gray-600 dark:text-zinc-400">Cargando usuarios...</p>
      </div>

      <div v-else-if="users.length === 0" class="py-8">
        <p class="text-[13px] text-gray-600 dark:text-zinc-400">No hay usuarios registrados.</p>
      </div>

      <div v-else class="space-y-4">
        <div v-for="user in users" :key="user.id" class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-zinc-800/50 last:border-0">
          <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
              <span class="text-sm font-semibold text-blue-700 dark:text-blue-400">{{ user.name.charAt(0).toUpperCase() }}</span>
            </div>
            <div>
              <p class="text-[14px] font-semibold text-gray-900 dark:text-white">{{ user.name }}</p>
              <p class="text-[13px] text-gray-600 dark:text-zinc-400">{{ user.email }} • <span v-if="user.role">{{ user.role.name || user.role }}</span></p>
            </div>
          </div>
          <div class="flex items-center gap-4">
            <span :class="user.active !== false ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'" class="text-[13px]">
              {{ user.active !== false ? 'Activo' : 'Inactivo' }}
            </span>
            <button @click="resetPassword(user)" class="text-[13px] text-blue-600 hover:underline">
              Restablecer contraseña
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- TAB: Productos MS365 Style -->
    <div v-if="activeTab === 'products'" class="mb-10">
      <div class="flex items-center justify-between mb-5">
        <h3 class="text-[15px] font-semibold text-gray-900 dark:text-white">Productos ({{ products.length }})</h3>
        <div class="flex items-center gap-4">
          <div class="relative">
            <input v-model="productSearch" type="text" placeholder="Buscar producto..." class="pl-8 pr-3 py-1.5 text-[13px] border-b border-gray-300 dark:border-zinc-700 bg-transparent text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:border-blue-600 w-48 transition-colors">
            <svg class="w-4 h-4 text-gray-500 absolute left-1 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          </div>
          <button @click="loadProducts" class="text-[13px] text-blue-600 hover:underline flex items-center gap-1.5" :class="{'opacity-50 cursor-not-allowed': loadingProducts}">
            <svg class="w-4 h-4" :class="{'animate-spin': loadingProducts}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Actualizar
          </button>
        </div>
      </div>

      <div v-if="loadingProducts" class="py-8">
        <p class="text-[13px] text-gray-600 dark:text-zinc-400">Cargando productos...</p>
      </div>

      <div v-else-if="filteredProducts.length === 0" class="py-8">
        <p class="text-[13px] text-gray-600 dark:text-zinc-400">{{ productSearch ? 'Sin resultados para esta búsqueda.' : 'No hay productos registrados.' }}</p>
      </div>

      <div v-else>
        <table class="w-full text-left">
          <thead>
            <tr class="border-b-2 border-gray-200 dark:border-zinc-800">
              <th class="py-3 px-2 text-[13px] font-semibold text-gray-900 dark:text-white">Producto</th>
              <th class="py-3 px-2 text-[13px] font-semibold text-gray-900 dark:text-white">Precio</th>
              <th class="py-3 px-2 text-[13px] font-semibold text-gray-900 dark:text-white">Stock</th>
              <th class="py-3 px-2 text-[13px] font-semibold text-gray-900 dark:text-white">Variantes</th>
              <th class="py-3 px-2 text-[13px] font-semibold text-gray-900 dark:text-white">Estado</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-zinc-800">
            <template v-for="product in paginatedProducts" :key="product.id">
              <tr class="hover:bg-gray-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                <td class="py-3 px-2">
                  <div class="flex items-center gap-3">
                    <div v-if="product.image_url" class="w-8 h-8 flex-shrink-0">
                      <img :src="product.image_url" :alt="product.name" class="w-full h-full object-cover rounded-md">
                    </div>
                    <span class="text-[13px] text-gray-900 dark:text-white">{{ product.name }}</span>
                  </div>
                </td>
                <td class="py-3 px-2">
                  <span class="text-[13px] text-gray-600 dark:text-zinc-400">${{ parseFloat(product.price || 0).toLocaleString() }}</span>
                </td>
                <td class="py-3 px-2">
                  <span class="text-[13px]" :class="(product.stock || 0) <= 5 ? 'text-rose-600' : 'text-gray-600 dark:text-zinc-400'">{{ product.stock || 0 }}</span>
                </td>
                <td class="py-3 px-2">
                  <div v-if="product.variants && product.variants.length > 0">
                    <button @click="toggleVariants(product.id)" class="text-[13px] text-blue-600 hover:underline flex items-center gap-1">
                      {{ product.variants.length }} variante{{ product.variants.length > 1 ? 's' : '' }}
                      <svg class="w-3 h-3 transition-transform" :class="expandedVariants[product.id] ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                  </div>
                  <span v-else class="text-[13px] text-gray-400">—</span>
                </td>
                <td class="py-3 px-2">
                  <span class="text-[13px]" :class="product.active ? 'text-emerald-600' : 'text-rose-600'">
                    {{ product.active ? 'Activo' : 'Inactivo' }}
                  </span>
                </td>
              </tr>
                <!-- Variants expandable row -->
                <tr v-if="product.variants && product.variants.length > 0 && expandedVariants[product.id]" :key="'v-' + product.id" class="bg-gray-50/50 dark:bg-zinc-800/20">
                  <td :colspan="5" class="px-5 py-2">
                    <div class="ml-12 space-y-1">
                      <div v-for="variant in product.variants" :key="variant.id" class="flex items-center gap-4 py-1.5 px-3 rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-800/50 transition-colors">
                        <span class="text-xs font-medium text-gray-700 dark:text-zinc-300 min-w-[120px]">{{ variant.name || variant.sku || 'Variante' }}</span>
                        <span v-if="variant.color" class="inline-flex items-center gap-1 text-[10px] text-gray-500 dark:text-zinc-400">
                          <span class="w-3 h-3 rounded-full border border-gray-200 dark:border-zinc-600" :style="{ backgroundColor: variant.color }"></span>
                          {{ variant.color_name || variant.color }}
                        </span>
                        <span v-if="variant.size" class="px-1.5 py-0.5 bg-gray-100 dark:bg-zinc-700 text-[10px] font-medium text-gray-600 dark:text-zinc-400 rounded">{{ variant.size }}</span>
                        <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">${{ parseFloat(variant.price || product.price || 0).toLocaleString() }}</span>
                        <span class="text-xs" :class="(variant.stock || 0) <= 5 ? 'text-rose-600 dark:text-rose-400 font-semibold' : 'text-gray-500 dark:text-zinc-400'">Stock: {{ variant.stock ?? 0 }}</span>
                      </div>
                    </div>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
          <div v-if="totalProductPages > 1" class="py-4 border-t border-gray-100 dark:border-zinc-800 flex items-center justify-between">
            <p class="text-[13px] text-gray-600 dark:text-zinc-400">Mostrando {{ productPageStart + 1 }}-{{ Math.min(productPageStart + productsPerPage, filteredProducts.length) }} de {{ filteredProducts.length }}</p>
            <div class="flex items-center gap-2">
              <button @click="productPage = Math.max(0, productPage - 1)" :disabled="productPage === 0" class="text-[13px] text-blue-600 hover:underline disabled:opacity-40 disabled:hover:no-underline disabled:cursor-not-allowed">Anterior</button>
              <span class="text-[13px] text-gray-400 dark:text-zinc-500">{{ productPage + 1 }} / {{ totalProductPages }}</span>
              <button @click="productPage = Math.min(totalProductPages - 1, productPage + 1)" :disabled="productPage >= totalProductPages - 1" class="text-[13px] text-blue-600 hover:underline disabled:opacity-40 disabled:hover:no-underline disabled:cursor-not-allowed">Siguiente</button>
            </div>
          </div>
        </div>
    </div>

    <!-- TAB: Suscripción -->
    <div v-if="activeTab === 'subscription'" class="space-y-6">
      
      <!-- Estado Actual + Plan en una sola card compacta -->
      <div class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 dark:border-zinc-800">
          <h3 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Estado de Suscripción</h3>
        </div>
        <div class="grid grid-cols-5 divide-x divide-gray-100 dark:divide-zinc-800">
          <div class="px-4 py-3">
            <p class="text-[10px] text-gray-400 dark:text-zinc-500 uppercase tracking-wider mb-1">Plan</p>
            <span class="text-[11px] font-semibold text-gray-900 dark:text-white">
              {{ (tenant.plan || 'N/A').replace('_', ' ').toUpperCase() }}
            </span>
          </div>
          <div class="px-4 py-3">
            <p class="text-[10px] text-gray-400 dark:text-zinc-500 uppercase tracking-wider mb-1">Estado</p>
            <div class="flex items-center gap-1.5">
              <span :class="statusDot" class="w-2 h-2 rounded-full"></span>
              <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ statusLabel }}</span>
            </div>
          </div>
          <div class="px-4 py-3">
            <p class="text-[10px] text-gray-400 dark:text-zinc-500 uppercase tracking-wider mb-1">Inicio</p>
            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ tenant.subscription_start ? formatDate(tenant.subscription_start) : '—' }}</p>
          </div>
          <div class="px-4 py-3">
            <p class="text-[10px] text-gray-400 dark:text-zinc-500 uppercase tracking-wider mb-1">Vencimiento</p>
            <p class="text-sm font-semibold" :class="isExpired ? 'text-rose-600 dark:text-rose-400' : isExpiringSoon ? 'text-amber-600 dark:text-amber-400' : 'text-gray-900 dark:text-white'">
              {{ tenant.subscription_end ? formatDate(tenant.subscription_end) : '—' }}
            </p>
          </div>
          <div class="px-4 py-3">
            <p class="text-[10px] text-gray-400 dark:text-zinc-500 uppercase tracking-wider mb-1">Restante</p>
            <p class="text-sm font-bold" :class="isExpired ? 'text-rose-600 dark:text-rose-400' : daysRemaining <= 7 ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400'">
              {{ daysRemaining !== null ? (isExpired ? 'Expirado' : daysRemaining + 'd') : '—' }}
            </p>
          </div>
        </div>
      </div>

      <!-- Cambiar Plan + Límites en una fila -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Cambiar Plan -->
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 overflow-hidden">
          <div class="px-5 py-3 border-b border-gray-100 dark:border-zinc-800">
            <h3 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Cambiar Plan</h3>
          </div>
          <div class="p-3">
            <div class="grid grid-cols-2 gap-2">
              <button v-for="p in plans" :key="p.key" @click="requestChangePlan(p.key)" 
                      :class="[
                        'relative px-3 py-2.5 rounded-lg border text-left transition-all duration-150',
                        tenant.plan === p.key 
                          ? 'border-blue-600 dark:border-blue-500 bg-blue-50/50 dark:bg-blue-900/20 shadow-sm' 
                          : 'border-gray-200 dark:border-zinc-700 hover:border-gray-300 dark:hover:border-zinc-600 hover:bg-gray-50 dark:hover:bg-zinc-800/50'
                      ]">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-xs font-bold text-gray-900 dark:text-white">{{ p.label }}</p>
                    <p class="text-[10px] text-gray-400 dark:text-zinc-500">{{ p.price }}</p>
                  </div>
                  <svg v-if="tenant.plan === p.key" class="w-4 h-4 text-blue-500 dark:text-blue-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                  </svg>
                </div>
              </button>
            </div>
          </div>
        </div>

        <!-- Límites -->
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 overflow-hidden">
          <div class="px-5 py-3 border-b border-gray-100 dark:border-zinc-800 flex items-center justify-between">
            <h3 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Límites</h3>
            <div v-if="limitsChanged" class="flex items-center gap-1.5">
              <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
              <span class="text-[10px] text-amber-600 dark:text-amber-400 font-medium">Sin guardar</span>
            </div>
          </div>
          <div class="p-6 space-y-5">
            <!-- Max Usuarios inline -->
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/50 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-gray-900 dark:text-white">Máx. Usuarios</p>
                <p v-if="tenant.stats?.total_users" class="text-[10px] text-gray-400 dark:text-zinc-500">Actual: {{ tenant.stats.total_users }}</p>
              </div>
              <input type="number" v-model.number="editLimits.max_users" min="1" max="999" placeholder="∞"
                     class="w-16 px-2 py-1.5 text-xs font-bold text-center bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg text-gray-900 dark:text-white placeholder-gray-300 dark:placeholder-zinc-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
              <div class="flex gap-0.5">
                <button v-for="preset in [3, 5, 10, 20]" :key="preset" @click="editLimits.max_users = preset"
                        :class="editLimits.max_users === preset ? 'bg-blue-600 text-white border-blue-600' : 'bg-white dark:bg-zinc-800 text-gray-500 dark:text-zinc-400 border-gray-200 dark:border-zinc-700 hover:border-gray-300'"
                        class="w-7 h-7 flex items-center justify-center text-[10px] font-bold rounded border transition-all">
                  {{ preset }}
                </button>
              </div>
            </div>
            <!-- Max Sedes inline -->
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-950/50 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-purple-600 dark:purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.15c0 .415.336.75.75.75z"/></svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-gray-900 dark:text-white">Máx. Sedes</p>
                <p class="text-[10px] text-gray-400 dark:text-zinc-500">Bodegas/sucursales</p>
              </div>
              <input type="number" v-model.number="editLimits.max_warehouses" min="1" max="99" placeholder="∞"
                     class="w-16 px-2 py-1.5 text-xs font-bold text-center bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg text-gray-900 dark:text-white placeholder-gray-300 dark:placeholder-zinc-600 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
              <div class="flex gap-0.5">
                <button v-for="preset in [1, 3, 5, 10]" :key="preset" @click="editLimits.max_warehouses = preset"
                        :class="editLimits.max_warehouses === preset ? 'bg-purple-600 text-white border-purple-600' : 'bg-white dark:bg-zinc-800 text-gray-500 dark:text-zinc-400 border-gray-200 dark:border-zinc-700 hover:border-gray-300'"
                        class="w-7 h-7 flex items-center justify-center text-[10px] font-bold rounded border transition-all">
                  {{ preset }}
                </button>
              </div>
            </div>
            <!-- Guardar -->
            <button @click="saveLimits" :disabled="savingLimits || !limitsChanged"
                    :class="limitsChanged ? 'bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600' : 'bg-gray-200 dark:bg-zinc-800 cursor-not-allowed'"
                    class="w-full py-2 text-white font-semibold text-xs rounded-lg transition-all flex items-center justify-center gap-1.5 disabled:opacity-50">
              <svg v-if="savingLimits" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
              {{ savingLimits ? 'Guardando...' : 'Guardar Límites' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Fechas + Pausar en una fila -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Modificar Fechas (2/3) -->
        <div class="lg:col-span-2 bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 overflow-hidden">
          <div class="px-5 py-3 border-b border-gray-100 dark:border-zinc-800 flex items-center gap-2">
            <svg class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
            <h3 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Fechas del Plan</h3>
          </div>
          <div class="p-4">
            <div class="flex items-end gap-3 mb-3">
              <div class="flex-1">
                <label class="block text-[10px] font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-1">Inicio</label>
                <input type="date" v-model="editSubscription.start" 
                       class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
              </div>
              <div class="flex-1">
                <label class="block text-[10px] font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-1">Vencimiento</label>
                <input type="date" v-model="editSubscription.end" 
                       class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-zinc-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
              </div>
              <button @click="updateSubscriptionDates" :disabled="savingDates" 
                      class="px-5 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white font-semibold text-xs rounded-lg transition-all disabled:opacity-50 flex items-center gap-1.5 flex-shrink-0">
                <svg v-if="savingDates" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                {{ savingDates ? 'Guardando...' : 'Guardar' }}
              </button>
            </div>
            <div class="flex flex-wrap gap-1.5">
              <button v-for="ext in quickExtensions" :key="ext.days" @click="extendDays(ext.days)" 
                      class="px-2.5 py-1 bg-gray-50 dark:bg-zinc-800 text-gray-500 dark:text-zinc-400 text-[11px] font-semibold rounded border border-gray-200 dark:border-zinc-700 hover:bg-gray-100 dark:hover:bg-zinc-700 hover:border-gray-300 dark:hover:border-zinc-600 transition-all">
                {{ ext.label }}
              </button>
            </div>
          </div>
        </div>

        <!-- Pausar / Activar (1/3) -->
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 overflow-hidden">
          <div class="px-5 py-3 border-b border-gray-100 dark:border-zinc-800">
            <div class="flex items-center gap-1.5">
              <svg v-if="tenant.status === 'active'" class="w-3.5 h-3.5 text-gray-700 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.25 9v6m-4.5 0V9M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <svg v-else class="w-3.5 h-3.5 text-gray-700 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15.91 11.672a.375.375 0 010 .656l-5.603 3.113a.375.375 0 01-.557-.328V8.887c0-.286.307-.466.557-.327l5.603 3.112z"/></svg>
              <h3 class="text-xs font-bold uppercase tracking-wider text-gray-900 dark:text-white">
                {{ tenant.status === 'active' ? 'Pausar' : 'Activar' }}
              </h3>
            </div>
          </div>
          <div class="p-4">
            <p class="text-[11px] text-gray-500 dark:text-zinc-400 mb-3 leading-relaxed">
              {{ tenant.status === 'active' ? 'Pausar impedirá el acceso a la tienda. Los datos se conservan.' : 'Activar permitirá el acceso normalmente.' }}
            </p>
            <button @click="toggleStatus" 
                    :class="tenant.status === 'active' ? 'border border-gray-300 dark:border-zinc-700 text-gray-700 dark:text-zinc-300 hover:bg-gray-50 dark:hover:bg-zinc-800' : 'bg-blue-600 hover:bg-blue-700 text-white'" 
                    class="w-full py-2 font-semibold text-xs rounded-lg transition-all">
              {{ tenant.status === 'active' ? 'Pausar Tienda' : 'Activar Tienda' }}
            </button>
          </div>
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

    <!-- Inline: Confirmar Cambio de Plan -->
    <div v-if="showPlanConfirm" class="fixed inset-0 bg-black/40 dark:bg-black/60 flex items-center justify-center z-50 p-4" @click.self="showPlanConfirm = false">
      <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 max-w-md w-full border border-gray-200 dark:border-zinc-800 shadow-2xl">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 bg-blue-50 dark:bg-blue-950 rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
          </div>
          <div>
            <h4 class="text-sm font-bold text-gray-900 dark:text-white">Confirmar Cambio de Plan</h4>
            <p class="text-xs text-gray-400 dark:text-zinc-500">{{ tenant.business_name || tenant.name }}</p>
          </div>
        </div>
        <div class="bg-gray-50 dark:bg-zinc-800 rounded-xl p-4 mb-4">
          <div class="flex items-center justify-between">
            <div class="text-center flex-1">
              <p class="text-[10px] text-gray-400 dark:text-zinc-500 uppercase tracking-wider mb-1">Plan Actual</p>
              <span :class="planBadgeClasses" class="inline-flex px-2.5 py-1 rounded-lg text-xs font-bold border">
                {{ (tenant.plan || 'N/A').replace('_', ' ').toUpperCase() }}
              </span>
            </div>
            <svg class="w-5 h-5 text-gray-300 dark:text-zinc-600 mx-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            <div class="text-center flex-1">
              <p class="text-[10px] text-gray-400 dark:text-zinc-500 uppercase tracking-wider mb-1">Nuevo Plan</p>
              <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-bold border bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400 border-blue-200 dark:border-blue-800">
                {{ (pendingPlanChange || '').replace('_', ' ').toUpperCase() }}
              </span>
            </div>
          </div>
        </div>
        <p class="text-sm text-gray-500 dark:text-zinc-400 mb-5">¿Estás seguro de que deseas cambiar el plan de este tenant? Los límites y funcionalidades se ajustarán según el nuevo plan.</p>
        <div class="flex gap-2">
          <button @click="showPlanConfirm = false; pendingPlanChange = null" class="flex-1 px-4 py-2.5 bg-gray-100 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 rounded-lg font-medium text-sm transition-colors">Cancelar</button>
          <button @click="confirmPlanChange" class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium text-sm transition-colors">Confirmar Cambio</button>
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
const showPlanConfirm = ref(false)
const pendingPlanChange = ref(null)
const catalogCopied = ref(false)
const expandedVariants = ref({})
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
    'free_trial': 'text-gray-600 dark:text-zinc-400 border-gray-200 dark:border-zinc-700',
    'basic': 'text-blue-600 dark:text-blue-400 border-gray-200 dark:border-zinc-700',
    'premium': 'text-indigo-600 dark:text-indigo-400 border-gray-200 dark:border-zinc-700',
    'enterprise': 'text-gray-900 dark:text-white border-gray-300 dark:border-zinc-600'
  }
  return badges[props.tenant.plan] || badges.free_trial
})

const statusClasses = computed(() => {
  const s = props.tenant.status
  if (s === 'active') return 'text-gray-700 dark:text-zinc-300 border-gray-200 dark:border-zinc-700'
  if (s === 'paused') return 'text-gray-500 dark:text-zinc-400 border-gray-200 dark:border-zinc-700'
  return 'text-rose-600 dark:text-rose-400 border-gray-200 dark:border-zinc-700'
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
    const res = await axios.get(`/api/admin/api/tenants/${props.tenant.id}/users`)
    if (res.data.success) users.value = res.data.data
  } catch { /* silently fail */ }
  loadingUsers.value = false
}

const loadProducts = async () => {
  loadingProducts.value = true
  try {
    const res = await axios.get(`/api/admin/api/tenants/${props.tenant.id}/products`)
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

const requestChangePlan = (plan) => {
  if (plan === props.tenant.plan) return
  pendingPlanChange.value = plan
  showPlanConfirm.value = true
}

const confirmPlanChange = () => {
  if (pendingPlanChange.value) {
    changePlan(pendingPlanChange.value)
  }
  showPlanConfirm.value = false
  pendingPlanChange.value = null
}

const toggleVariants = (productId) => {
  expandedVariants.value[productId] = !expandedVariants.value[productId]
}

const hasCatalogAccess = computed(() => {
  const plan = props.tenant.plan
  return plan === 'premium' || plan === 'enterprise' || plan === 'basic'
})

const catalogUrl = computed(() => {
  const domain = props.tenant.primary_domain || props.tenant.domain
  return `https://${domain}/catalog`
})

const catalogDisabledReason = computed(() => {
  const plan = props.tenant.plan
  if (plan === 'free_trial') return 'Requiere plan Basic o superior'
  return 'Catálogo no habilitado'
})

const copyCatalogUrl = () => {
  navigator.clipboard.writeText(catalogUrl.value)
  catalogCopied.value = true
  setTimeout(() => { catalogCopied.value = false }, 2000)
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
