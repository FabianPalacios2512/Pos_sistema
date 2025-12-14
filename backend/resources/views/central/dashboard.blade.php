<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard - God Mode</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div id="app"></div>

    <script type="module">
        import { createApp, ref, onMounted, computed } from 'https://unpkg.com/vue@3/dist/vue.esm-browser.js'
        import axios from 'https://cdn.skypack.dev/axios'

        // Configurar axios con CSRF token
        const token = document.querySelector('meta[name="csrf-token"]').content
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

        createApp({
            setup() {
                const loading = ref(false)
                const kpis = ref({ total_active_clients: 0, clients_created_today: 0, mrr: 0, ai_tokens_this_month: 0, ai_cost_this_month: 0 })
                const tenants = ref({ data: [], total: 0 })
                const selectedTenant = ref(null)
                const showCreateModal = ref(false)
                const showDetailsModal = ref(false)
                const showDeleteModal = ref(false)
                const tenantToDelete = ref(null)
                const searchQuery = ref('')

                // Formulario de creación
                const newTenant = ref({
                    tenant_id: '',
                    business_name: '',
                    domain: '',
                    plan: 'free_trial'
                })

                const fetchData = async () => {
                    loading.value = true
                    try {
                        const [kpisRes, tenantsRes] = await Promise.all([
                            axios.get('/admin/api/kpis'),
                            axios.get('/admin/api/tenants')
                        ])
                        if (kpisRes.data.success) kpis.value = kpisRes.data.data
                        if (tenantsRes.data.success) tenants.value = tenantsRes.data.data
                    } catch (error) {
                        console.error('Error:', error)
                        alert('Error al cargar datos')
                    }
                    loading.value = false
                }

                const createTenant = async () => {
                    try {
                        const res = await axios.post('/admin/api/tenants', newTenant.value)
                        if (res.data.success) {
                            alert('✅ ' + res.data.message)
                            showCreateModal.value = false
                            newTenant.value = { tenant_id: '', business_name: '', domain: '', plan: 'free_trial' }
                            fetchData()
                        }
                    } catch (error) {
                        alert('❌ Error: ' + (error.response?.data?.message || error.message))
                    }
                }

                const viewTenantDetails = async (tenant) => {
                    try {
                        const res = await axios.get(`/admin/api/tenants/${tenant.id}`)
                        if (res.data.success) {
                            selectedTenant.value = res.data.data
                            showDetailsModal.value = true
                        }
                    } catch (error) {
                        alert('❌ Error al cargar detalles: ' + error.message)
                    }
                }

                const updateTenantPlan = async (tenantId, newPlan) => {
                    try {
                        const res = await axios.put(`/admin/api/tenants/${tenantId}`, { plan: newPlan })
                        if (res.data.success) {
                            alert('✅ Plan actualizado exitosamente')
                            fetchData()
                            if (selectedTenant.value?.id === tenantId) {
                                selectedTenant.value.plan = newPlan
                            }
                        }
                    } catch (error) {
                        alert('❌ Error: ' + (error.response?.data?.message || error.message))
                    }
                }

                const toggleTenantStatus = async (tenantId, currentStatus) => {
                    const newStatus = currentStatus === 'active' ? 'paused' : 'active'
                    try {
                        const res = await axios.put(`/admin/api/tenants/${tenantId}`, { status: newStatus })
                        if (res.data.success) {
                            alert(`✅ Tienda ${newStatus === 'paused' ? 'pausada' : 'activada'} exitosamente`)
                            fetchData()
                            if (selectedTenant.value?.id === tenantId) {
                                selectedTenant.value.status = newStatus
                            }
                        }
                    } catch (error) {
                        alert('❌ Error: ' + (error.response?.data?.message || error.message))
                    }
                }

                const confirmDelete = (tenant) => {
                    tenantToDelete.value = tenant
                    showDeleteModal.value = true
                }

                const deleteTenant = async () => {
                    if (!tenantToDelete.value) return

                    try {
                        const res = await axios.delete(`/admin/api/tenants/${tenantToDelete.value.id}`)
                        if (res.data.success) {
                            alert('✅ ' + res.data.message)
                            showDeleteModal.value = false
                            tenantToDelete.value = null
                            fetchData()
                        }
                    } catch (error) {
                        alert('❌ Error: ' + (error.response?.data?.message || error.message))
                    }
                }

                const formatNumber = (num) => new Intl.NumberFormat('es-ES').format(num)
                const formatCurrency = (num) => '$' + new Intl.NumberFormat('es-ES', { minimumFractionDigits: 2 }).format(num)

                const getStatusBadge = (status) => {
                    const badges = {
                        'active': 'bg-emerald-500/20 text-emerald-400',
                        'paused': 'bg-amber-500/20 text-amber-400',
                        'suspended': 'bg-red-500/20 text-red-400'
                    }
                    return badges[status] || badges.active
                }

                const getPlanBadge = (plan) => {
                    const badges = {
                        'free_trial': 'bg-gray-700 text-gray-300',
                        'basic': 'bg-blue-600 text-white',
                        'premium': 'bg-purple-600 text-white',
                        'enterprise': 'bg-amber-600 text-white'
                    }
                    return badges[plan] || badges.free_trial
                }

                const filteredTenants = computed(() => {
                    if (!searchQuery.value) return tenants.value.data
                    return tenants.value.data.filter(t =>
                        t.business_name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                        t.id.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                        t.primary_domain.toLowerCase().includes(searchQuery.value.toLowerCase())
                    )
                })

                onMounted(() => {
                    fetchData()
                    setInterval(fetchData, 60000) // Auto-refresh cada minuto
                })

                return {
                    loading, kpis, tenants, filteredTenants, selectedTenant, showCreateModal, showDetailsModal,
                    showDeleteModal, tenantToDelete, newTenant, searchQuery,
                    fetchData, createTenant, viewTenantDetails, updateTenantPlan, toggleTenantStatus,
                    confirmDelete, deleteTenant, formatNumber, formatCurrency, getStatusBadge, getPlanBadge
                }
            },
            template: `
                <div class="min-h-screen bg-gradient-to-br from-gray-900 via-slate-900 to-gray-800 p-6">
                    <!-- Header -->
                    <header class="bg-gray-900 rounded-xl p-6 mb-6 border border-red-500/30 shadow-xl">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-14 h-14 bg-gradient-to-br from-red-600 to-red-700 rounded-xl flex items-center justify-center shadow-lg">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h1 class="text-3xl font-bold text-white flex items-center">
                                        GOD MODE
                                        <span class="px-3 py-1 bg-red-600 text-xs rounded-full ml-3 animate-pulse">ADMIN</span>
                                    </h1>
                                    <p class="text-sm text-gray-400 mt-1">Panel de Administración Central - Control Total</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <button @click="showCreateModal = true" class="px-6 py-3 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white rounded-xl font-bold shadow-lg transition-all duration-200 flex items-center space-x-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    <span>Nueva Tienda</span>
                                </button>
                                <button @click="fetchData" class="px-4 py-3 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-xl transition-all">
                                    <svg class="w-5 h-5" :class="{'animate-spin': loading}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </header>

                    <!-- KPIs -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-6 border border-emerald-500/30 shadow-lg hover:shadow-emerald-500/20 transition-all">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-xs text-gray-400 uppercase font-semibold tracking-wide">Clientes Activos</p>
                                <div class="w-10 h-10 bg-emerald-500/20 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-4xl font-bold text-white mb-2">@{{ kpis.total_active_clients }}</p>
                            <p class="text-xs text-emerald-400 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                </svg>
                                +@{{ kpis.clients_created_today }} hoy
                            </p>
                        </div>

                        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-6 border border-blue-500/30 shadow-lg hover:shadow-blue-500/20 transition-all">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-xs text-gray-400 uppercase font-semibold tracking-wide">Ingresos MRR</p>
                                <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-4xl font-bold text-white mb-2">\$@{{ formatNumber(kpis.mrr) }}</p>
                            <p class="text-xs text-blue-400">USD/mes</p>
                        </div>

                        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-6 border border-purple-500/30 shadow-lg hover:shadow-purple-500/20 transition-all">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-xs text-gray-400 uppercase font-semibold tracking-wide">Tokens IA (Mes)</p>
                                <div class="w-10 h-10 bg-purple-500/20 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-4xl font-bold text-white mb-2">@{{ formatNumber(kpis.ai_tokens_this_month) }}</p>
                            <p class="text-xs text-purple-400">\$@{{ kpis.ai_cost_this_month.toFixed(2) }} costo</p>
                        </div>

                        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-6 border border-orange-500/30 shadow-lg hover:shadow-orange-500/20 transition-all">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-xs text-gray-400 uppercase font-semibold tracking-wide">Tiendas Nuevas</p>
                                <div class="w-10 h-10 bg-orange-500/20 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-4xl font-bold text-white mb-2">@{{ kpis.clients_created_today }}</p>
                            <p class="text-xs text-orange-400">Hoy</p>
                        </div>
                    </div>

                    <!-- Tabla de Tenants -->
                    <div class="bg-gray-900 rounded-xl border border-gray-700 shadow-xl">
                        <div class="bg-gray-800 border-b border-gray-700 px-6 py-5 flex items-center justify-between">
                            <div>
                                <h2 class="text-xl font-bold text-white">Gestión de Tiendas</h2>
                                <p class="text-sm text-gray-400 mt-1">@{{ tenants.total }} inquilinos registrados</p>
                            </div>
                            <div class="relative">
                                <input v-model="searchQuery" type="text" placeholder="Buscar tienda..." class="pl-10 pr-4 py-2 bg-gray-700 text-white rounded-lg border border-gray-600 focus:border-blue-500 focus:outline-none">
                                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-800">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">Negocio</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">Dominio</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">Plan</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">Estado</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">Fecha</th>
                                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-300 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-800">
                                    <tr v-for="tenant in filteredTenants" :key="tenant.id" class="hover:bg-gray-800/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div>
                                                <p class="text-sm font-bold text-white">@{{ tenant.business_name }}</p>
                                                <p class="text-xs text-gray-500 font-mono">@{{ tenant.id }}</p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <a :href="'http://' + tenant.primary_domain" target="_blank" class="text-sm text-blue-400 hover:text-blue-300 hover:underline">
                                                @{{ tenant.primary_domain }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span :class="getPlanBadge(tenant.plan)" class="px-3 py-1 text-xs font-bold rounded-full">
                                                @{{ tenant.plan.toUpperCase() }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span :class="getStatusBadge(tenant.status)" class="px-3 py-1 text-xs font-bold rounded-full">
                                                @{{ tenant.status === 'active' ? 'ACTIVO' : tenant.status === 'paused' ? 'PAUSADO' : 'SUSPENDIDO' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-sm text-gray-400">@{{ new Date(tenant.created_at).toLocaleDateString('es-ES') }}</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-center space-x-2">
                                                <button @click="viewTenantDetails(tenant)" class="p-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors" title="Ver detalles">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </button>
                                                <button @click="toggleTenantStatus(tenant.id, tenant.status)" :class="tenant.status === 'active' ? 'bg-amber-600 hover:bg-amber-700' : 'bg-emerald-600 hover:bg-emerald-700'" class="p-2 text-white rounded-lg transition-colors" :title="tenant.status === 'active' ? 'Pausar' : 'Activar'">
                                                    <svg v-if="tenant.status === 'active'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </button>
                                                <a :href="'http://' + tenant.primary_domain + '/login'" target="_blank" class="p-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors" title="Entrar a la tienda">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                                    </svg>
                                                </a>
                                                <button @click="confirmDelete(tenant)" class="p-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors" title="Eliminar">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Modal: Crear Tienda -->
                    <div v-if="showCreateModal" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4">
                        <div class="bg-gray-900 rounded-2xl p-8 max-w-lg w-full border border-gray-700 shadow-2xl">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-2xl font-bold text-white">Crear Nueva Tienda</h3>
                                <button @click="showCreateModal = false" class="text-gray-400 hover:text-white">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-300 mb-2">ID de Tenant (slug)</label>
                                    <input v-model="newTenant.tenant_id" type="text" placeholder="mi-tienda" class="w-full px-4 py-3 bg-gray-800 text-white rounded-lg border border-gray-700 focus:border-blue-500 focus:outline-none" required>
                                    <p class="text-xs text-gray-500 mt-1">Solo letras minúsculas, números y guiones</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-300 mb-2">Nombre del Negocio</label>
                                    <input v-model="newTenant.business_name" type="text" placeholder="Mi Tienda S.A." class="w-full px-4 py-3 bg-gray-800 text-white rounded-lg border border-gray-700 focus:border-blue-500 focus:outline-none" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-300 mb-2">Dominio</label>
                                    <input v-model="newTenant.domain" type="text" placeholder="mi-tienda.localhost" class="w-full px-4 py-3 bg-gray-800 text-white rounded-lg border border-gray-700 focus:border-blue-500 focus:outline-none" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-300 mb-2">Plan</label>
                                    <select v-model="newTenant.plan" class="w-full px-4 py-3 bg-gray-800 text-white rounded-lg border border-gray-700 focus:border-blue-500 focus:outline-none">
                                        <option value="free_trial">Free Trial</option>
                                        <option value="basic">Basic</option>
                                        <option value="premium">Premium</option>
                                        <option value="enterprise">Enterprise</option>
                                    </select>
                                </div>
                                <div class="flex space-x-3 pt-4">
                                    <button @click="showCreateModal = false" class="flex-1 px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg font-semibold transition-colors">
                                        Cancelar
                                    </button>
                                    <button @click="createTenant" class="flex-1 px-6 py-3 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white rounded-lg font-bold shadow-lg transition-all">
                                        Crear Tienda
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal: Detalles de Tienda -->
                    <div v-if="showDetailsModal && selectedTenant" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4 overflow-y-auto">
                        <div class="bg-gray-900 rounded-2xl p-8 max-w-4xl w-full border border-gray-700 shadow-2xl my-8">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h3 class="text-3xl font-bold text-white">@{{ selectedTenant.business_name }}</h3>
                                    <p class="text-sm text-gray-400 mt-1">ID: @{{ selectedTenant.id }}</p>
                                </div>
                                <button @click="showDetailsModal = false" class="text-gray-400 hover:text-white">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <!-- Info General -->
                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div class="bg-gray-800 p-4 rounded-lg">
                                    <p class="text-xs text-gray-400 mb-1">Plan Actual</p>
                                    <div class="flex items-center justify-between">
                                        <span :class="getPlanBadge(selectedTenant.plan)" class="px-3 py-1 text-xs font-bold rounded-full">
                                            @{{ selectedTenant.plan.toUpperCase() }}
                                        </span>
                                        <select @change="updateTenantPlan(selectedTenant.id, $event.target.value)" :value="selectedTenant.plan" class="px-3 py-1 bg-gray-700 text-white rounded text-xs border border-gray-600">
                                            <option value="free_trial">Free Trial</option>
                                            <option value="basic">Basic</option>
                                            <option value="premium">Premium</option>
                                            <option value="enterprise">Enterprise</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="bg-gray-800 p-4 rounded-lg">
                                    <p class="text-xs text-gray-400 mb-1">Estado</p>
                                    <span :class="getStatusBadge(selectedTenant.status)" class="px-3 py-1 text-xs font-bold rounded-full">
                                        @{{ selectedTenant.status === 'active' ? 'ACTIVO' : selectedTenant.status === 'paused' ? 'PAUSADO' : 'SUSPENDIDO' }}
                                    </span>
                                </div>
                                <div class="bg-gray-800 p-4 rounded-lg">
                                    <p class="text-xs text-gray-400 mb-1">Dominio Principal</p>
                                    <p class="text-sm text-white font-mono">@{{ selectedTenant.primary_domain }}</p>
                                </div>
                                <div class="bg-gray-800 p-4 rounded-lg">
                                    <p class="text-xs text-gray-400 mb-1">Creado</p>
                                    <p class="text-sm text-white">@{{ new Date(selectedTenant.created_at).toLocaleString('es-ES') }}</p>
                                </div>
                            </div>

                            <!-- Estadísticas -->
                            <div v-if="selectedTenant.stats && !selectedTenant.stats.error" class="mb-6">
                                <h4 class="text-lg font-bold text-white mb-4">Estadísticas de la Tienda</h4>
                                <div class="grid grid-cols-4 gap-4">
                                    <div class="bg-gray-800 p-4 rounded-lg text-center">
                                        <p class="text-2xl font-bold text-emerald-400">@{{ selectedTenant.stats.total_sales || 0 }}</p>
                                        <p class="text-xs text-gray-400 mt-1">Ventas Totales</p>
                                    </div>
                                    <div class="bg-gray-800 p-4 rounded-lg text-center">
                                        <p class="text-2xl font-bold text-blue-400">@{{ formatCurrency(selectedTenant.stats.total_revenue || 0) }}</p>
                                        <p class="text-xs text-gray-400 mt-1">Ingresos</p>
                                    </div>
                                    <div class="bg-gray-800 p-4 rounded-lg text-center">
                                        <p class="text-2xl font-bold text-purple-400">@{{ selectedTenant.stats.total_products || 0 }}</p>
                                        <p class="text-xs text-gray-400 mt-1">Productos</p>
                                    </div>
                                    <div class="bg-gray-800 p-4 rounded-lg text-center">
                                        <p class="text-2xl font-bold text-amber-400">@{{ selectedTenant.stats.total_customers || 0 }}</p>
                                        <p class="text-xs text-gray-400 mt-1">Clientes</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Uso de IA -->
                            <div class="mb-6">
                                <h4 class="text-lg font-bold text-white mb-4 flex items-center justify-between">
                                    <span>Consumo de IA</span>
                                    <span v-if="selectedTenant.ai_usage.limits" class="text-xs font-normal text-gray-400">
                                        Plan: @{{ selectedTenant.ai_usage.plan.toUpperCase() }}
                                    </span>
                                </h4>

                                <!-- Advertencias -->
                                <div v-if="selectedTenant.ai_usage.warnings && selectedTenant.ai_usage.warnings.length > 0" class="mb-4 space-y-2">
                                    <div v-for="warning in selectedTenant.ai_usage.warnings" :key="warning.type"
                                         :class="warning.severity === 'critical' ? 'bg-red-900/30 border-red-500' : 'bg-yellow-900/30 border-yellow-500'"
                                         class="p-3 rounded-lg border">
                                        <p class="text-sm font-semibold" :class="warning.severity === 'critical' ? 'text-red-400' : 'text-yellow-400'">
                                            ⚠️ @{{ warning.message }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Límites y Uso Actual -->
                                <div v-if="selectedTenant.ai_usage.limits && !selectedTenant.ai_usage.limits.unlimited" class="mb-4 bg-gray-800 rounded-lg p-4">
                                    <p class="text-xs text-gray-400 mb-3 font-semibold uppercase">Límites del Plan</p>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <p class="text-xs text-gray-500">Peticiones/Hora</p>
                                            <p class="text-sm font-bold text-white">@{{ selectedTenant.ai_usage.limits.limits.requests_per_hour }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500">Peticiones/Día</p>
                                            <p class="text-sm font-bold text-white">@{{ selectedTenant.ai_usage.limits.limits.requests_per_day }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500">Tokens/Petición</p>
                                            <p class="text-sm font-bold text-white">@{{ formatNumber(selectedTenant.ai_usage.limits.limits.tokens_per_request) }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500">Tokens/Día</p>
                                            <p class="text-sm font-bold text-white">@{{ formatNumber(selectedTenant.ai_usage.limits.limits.tokens_per_day) }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Uso en Última Hora -->
                                <div class="mb-4">
                                    <p class="text-sm text-gray-400 mb-2 font-semibold">Última Hora</p>
                                    <div class="grid grid-cols-3 gap-4">
                                        <div class="bg-gray-800 p-3 rounded-lg text-center">
                                            <p class="text-xl font-bold text-blue-400">@{{ selectedTenant.ai_usage.usage.last_hour.requests }}</p>
                                            <p class="text-xs text-gray-400 mt-1">Peticiones</p>
                                        </div>
                                        <div class="bg-gray-800 p-3 rounded-lg text-center">
                                            <p class="text-xl font-bold text-purple-400">@{{ formatNumber(selectedTenant.ai_usage.usage.last_hour.tokens) }}</p>
                                            <p class="text-xs text-gray-400 mt-1">Tokens</p>
                                        </div>
                                        <div class="bg-gray-800 p-3 rounded-lg text-center">
                                            <p class="text-xl font-bold" :class="selectedTenant.ai_usage.usage.last_hour.remaining_requests === 0 ? 'text-red-400' : 'text-emerald-400'">
                                                @{{ typeof selectedTenant.ai_usage.usage.last_hour.remaining_requests === 'number' ? selectedTenant.ai_usage.usage.last_hour.remaining_requests : '∞' }}
                                            </p>
                                            <p class="text-xs text-gray-400 mt-1">Restantes</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Uso Hoy -->
                                <div class="mb-4">
                                    <p class="text-sm text-gray-400 mb-2 font-semibold">Hoy</p>
                                    <div class="grid grid-cols-4 gap-4">
                                        <div class="bg-gray-800 p-3 rounded-lg text-center">
                                            <p class="text-xl font-bold text-blue-400">@{{ selectedTenant.ai_usage.usage.today.requests }}</p>
                                            <p class="text-xs text-gray-400 mt-1">Peticiones</p>
                                        </div>
                                        <div class="bg-gray-800 p-3 rounded-lg text-center">
                                            <p class="text-xl font-bold text-purple-400">@{{ formatNumber(selectedTenant.ai_usage.usage.today.tokens) }}</p>
                                            <p class="text-xs text-gray-400 mt-1">Tokens</p>
                                        </div>
                                        <div class="bg-gray-800 p-3 rounded-lg text-center">
                                            <p class="text-xl font-bold text-amber-400">\$@{{ selectedTenant.ai_usage.usage.today.cost.toFixed(4) }}</p>
                                            <p class="text-xs text-gray-400 mt-1">Costo</p>
                                        </div>
                                        <div class="bg-gray-800 p-3 rounded-lg text-center">
                                            <p class="text-xl font-bold" :class="selectedTenant.ai_usage.usage.today.remaining_requests === 0 ? 'text-red-400' : 'text-emerald-400'">
                                                @{{ typeof selectedTenant.ai_usage.usage.today.remaining_requests === 'number' ? selectedTenant.ai_usage.usage.today.remaining_requests : '∞' }}
                                            </p>
                                            <p class="text-xs text-gray-400 mt-1">Restantes</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Total Histórico -->
                                <div>
                                    <p class="text-sm text-gray-400 mb-2 font-semibold">Total Histórico</p>
                                    <div class="grid grid-cols-3 gap-4">
                                        <div class="bg-gray-800 p-4 rounded-lg text-center">
                                            <p class="text-2xl font-bold text-white">@{{ selectedTenant.ai_usage.usage.total.requests }}</p>
                                            <p class="text-xs text-gray-400 mt-1">Peticiones</p>
                                        </div>
                                        <div class="bg-gray-800 p-4 rounded-lg text-center">
                                            <p class="text-2xl font-bold text-white">@{{ formatNumber(selectedTenant.ai_usage.usage.total.tokens) }}</p>
                                            <p class="text-xs text-gray-400 mt-1">Tokens</p>
                                        </div>
                                        <div class="bg-gray-800 p-4 rounded-lg text-center">
                                            <p class="text-2xl font-bold text-white">\$@{{ selectedTenant.ai_usage.usage.total.cost.toFixed(4) }}</p>
                                            <p class="text-xs text-gray-400 mt-1">Costo Total</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Acciones -->
                            <div class="flex space-x-3 pt-4 border-t border-gray-800">
                                <a :href="'http://' + selectedTenant.primary_domain + '/login'" target="_blank" class="flex-1 px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-center rounded-lg font-bold transition-colors">
                                    Entrar a la Tienda
                                </a>
                                <button @click="toggleTenantStatus(selectedTenant.id, selectedTenant.status)" :class="selectedTenant.status === 'active' ? 'bg-amber-600 hover:bg-amber-700' : 'bg-emerald-600 hover:bg-emerald-700'" class="flex-1 px-6 py-3 text-white rounded-lg font-bold transition-colors">
                                    @{{ selectedTenant.status === 'active' ? 'Pausar Tienda' : 'Activar Tienda' }}
                                </button>
                                <button @click="showDetailsModal = false" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg font-semibold transition-colors">
                                    Cerrar
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal: Confirmar Eliminación -->
                    <div v-if="showDeleteModal && tenantToDelete" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4">
                        <div class="bg-gray-900 rounded-2xl p-8 max-w-md w-full border border-red-500/30 shadow-2xl">
                            <div class="text-center mb-6">
                                <div class="w-16 h-16 bg-red-600/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold text-white mb-2">¿Eliminar Tienda?</h3>
                                <p class="text-gray-400 mb-1">Esta acción eliminará permanentemente:</p>
                                <p class="text-white font-bold">@{{ tenantToDelete.business_name }}</p>
                                <p class="text-sm text-gray-500 mt-2">ID: @{{ tenantToDelete.id }}</p>
                                <p class="text-sm text-red-400 mt-4 font-semibold">⚠️ Esta acción NO se puede deshacer</p>
                            </div>
                            <div class="flex space-x-3">
                                <button @click="showDeleteModal = false" class="flex-1 px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg font-semibold transition-colors">
                                    Cancelar
                                </button>
                                <button @click="deleteTenant" class="flex-1 px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-bold shadow-lg transition-all">
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `
        }).mount('#app')
    </script>
</body>
</html>
