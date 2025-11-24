<template>
  <!-- Sidebar Profesional - Diseño Limpio con Colapso -->
  <div 
    class="sidebar-container fixed inset-y-0 left-0 z-50 transform transition-all duration-300 ease-in-out flex flex-col lg:translate-x-0"
    :style="{
      width: sidebarCollapsed ? '80px' : '240px',
      backgroundColor: '#FFFFFF',
      borderRight: '1px solid #E0E0E0'
    }"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
  >
    
    <!-- Logo y Marca del Sistema -->
    <div :style="sidebarCollapsed ? 'padding: 20px 12px; border-bottom: 1px solid #E0E0E0;' : 'padding: 20px 15px; border-bottom: 1px solid #E0E0E0;'">
      <div class="flex items-center" :class="sidebarCollapsed ? 'justify-center' : 'gap-3'">
        <!-- Logo Simple -->
        <div style="width: 40px; height: 40px; background-color: #007BFF; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
          <svg style="width: 24px; height: 24px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
          </svg>
        </div>
        
        <!-- Texto del Logo (oculto cuando está colapsado) -->
        <div v-show="!sidebarCollapsed">
          <h1 style="font-size: 18px; font-weight: 700; color: #1F2937; margin: 0; line-height: 1.2;">POS Enterprise</h1>
          <p style="font-size: 12px; color: #9CA3AF; margin: 0; line-height: 1.2;">Sistema Punto de Venta</p>
        </div>
      </div>
      
      <!-- Botón de Colapsar/Expandir -->
      <button
        @click="$emit('update:sidebarCollapsed', !sidebarCollapsed)"
        class="collapse-button"
        :class="sidebarCollapsed ? 'collapsed' : ''"
        style="margin-top: 15px; width: 100%;"
      >
        <svg style="width: 16px; height: 16px; transition: transform 0.3s ease;" :style="{ transform: sidebarCollapsed ? 'rotate(180deg)' : 'rotate(0deg)' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        <span v-show="!sidebarCollapsed" style="margin-left: 8px;">Contraer</span>
      </button>
    </div>

    <!-- Menú de Navegación Principal -->
    <nav class="flex-1 overflow-y-auto py-4" style="scrollbar-width: thin; scrollbar-color: #E0E0E0 transparent;">
      
      <!-- Dashboard -->
      <div :style="sidebarCollapsed ? 'padding: 0 12px;' : 'padding: 0 15px;'">
        <div
          @click="$emit('change-module', 'dashboard')"
          class="menu-item"
          :class="[currentModule === 'dashboard' ? 'active' : '', sidebarCollapsed ? 'collapsed' : '']"
          :title="sidebarCollapsed ? 'Dashboard' : ''"
        >
          <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
          </svg>
          <span v-show="!sidebarCollapsed" class="menu-text">Dashboard</span>
        </div>
      </div>

      <!-- OPERACIONES -->
      <div style="margin-top: 20px;" :style="sidebarCollapsed ? 'padding: 0 12px;' : 'padding: 0 15px;'">
        <h3 v-show="!sidebarCollapsed" class="section-title">OPERACIONES</h3>        <!-- Punto de Venta -->
        <button
          @click="$emit('change-module', 'pos')"
          :class="getMenuItemClass('pos')"
          class="menu-item"
        >
          <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
          </svg>
          <span>Punto de Venta</span>
        </button>

        <!-- Facturación -->
        <button
          @click="$emit('change-module', 'invoices')"
          :class="getMenuItemClass('invoices')"
          class="menu-item"
        >
          <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
          <span>Facturación</span>
        </button>

        <!-- Devoluciones -->
        <button
          @click="$emit('change-module', 'returns-management')"
          :class="getMenuItemClass('returns-management')"
          class="menu-item"
        >
          <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path>
          </svg>
          <span>Devoluciones</span>
        </button>
      </div>

      <!-- INVENTARIO -->
      <div style="margin-bottom: 20px;">
        <div class="section-title">INVENTARIO</div>
        
        <!-- Productos -->
        <button
          @click="$emit('change-module', 'products')"
          :class="getMenuItemClass('products')"
          class="menu-item"
        >
          <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
          </svg>
          <span>Productos</span>
        </button>

        <!-- Categorías -->
        <button
          @click="$emit('change-module', 'categories')"
          :class="getMenuItemClass('categories')"
          class="menu-item"
        >
          <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
          </svg>
          <span>Categorías</span>
        </button>

        <!-- Control de Stock -->
        <button
          @click="$emit('change-module', 'stock')"
          :class="getMenuItemClass('stock')"
          class="menu-item"
        >
          <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
          </svg>
          <span>Control de Stock</span>
        </button>

        <!-- Inventario Inteligente -->
        <button
          @click="$emit('change-module', 'intelligent_inventory')"
          :class="getMenuItemClass('intelligent_inventory')"
          class="menu-item"
        >
          <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
          </svg>
          <span>Inventario IA</span>
        </button>
      </div>

      <!-- RELACIONES -->
      <div style="margin-bottom: 20px;">
        <div class="section-title">RELACIONES</div>
        
        <!-- Clientes -->
        <button
          @click="$emit('change-module', 'customers')"
          :class="getMenuItemClass('customers')"
          class="menu-item"
        >
          <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
          </svg>
          <span>Clientes</span>
        </button>

        <!-- Proveedores -->
        <button
          @click="$emit('change-module', 'suppliers')"
          :class="getMenuItemClass('suppliers')"
          class="menu-item"
        >
          <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
          </svg>
          <span>Proveedores</span>
        </button>
      </div>

      <!-- SISTEMA -->
      <div style="margin-bottom: 20px;">
        <div class="section-title">SISTEMA</div>
        
        <!-- Usuarios -->
        <button
          @click="$emit('change-module', 'users')"
          :class="getMenuItemClass('users')"
          class="menu-item"
        >
          <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
          </svg>
          <span>Usuarios</span>
        </button>

        <!-- Panel Administrativo -->
        <button
          @click="$emit('change-module', 'cash-admin')"
          :class="getMenuItemClass('cash-admin')"
          class="menu-item"
        >
          <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <span>Panel Administrativo</span>
        </button>

        <!-- Reportes -->
        <button
          @click="$emit('change-module', 'reports')"
          :class="getMenuItemClass('reports')"
          class="menu-item"
        >
          <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
          <span>Reportes</span>
        </button>

        <!-- Configuración -->
        <button
          @click="$emit('change-module', 'settings')"
          :class="getMenuItemClass('settings')"
          class="menu-item"
        >
          <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
          </svg>
          <span>Configuración</span>
        </button>
      </div>
    </nav>

    <!-- Overlay para móvil -->
    <div
      v-if="sidebarOpen"
      @click="$emit('toggle-sidebar')"
      class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"
      style="margin-left: 240px;"
    ></div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue'

const props = defineProps({
  currentModule: {
    type: String,
    required: true
  },
  sidebarOpen: {
    type: Boolean,
    default: true
  },
  sidebarCollapsed: {
    type: Boolean,
    default: false
  }
})

defineEmits(['change-module', 'toggle-sidebar', 'update:sidebarCollapsed'])

const getMenuItemClass = (module) => {
  const isActive = props.currentModule === module
  return isActive ? 'menu-item-active' : ''
}
</script>

<style scoped>
/* Títulos de sección */
.section-title {
  text-transform: uppercase;
  font-size: 11px;
  font-weight: 600;
  color: #9CA3AF;
  margin-top: 20px;
  margin-bottom: 8px;
  padding-left: 15px;
  letter-spacing: 0.05em;
}

/* Elementos del menú */
.menu-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 15px;
  margin: 4px 10px;
  border-radius: 6px;
  background-color: transparent;
  color: #6B7280;
  font-size: 14px;
  font-weight: 500;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
  width: calc(100% - 20px);
  text-align: left;
}

/* Estado hover */
.menu-item:hover {
  background-color: #F0F2F5;
  color: #007BFF;
}

.menu-item:hover svg {
  color: #007BFF;
}

/* Estado activo */
.menu-item-active {
  background-color: #E0E7FF !important;
  color: #007BFF !important;
  font-weight: 600;
  border-left: 3px solid #007BFF;
  padding-left: 12px;
}

.menu-item-active svg {
  color: #007BFF;
}

/* Scrollbar personalizado */
nav::-webkit-scrollbar {
  width: 6px;
}

nav::-webkit-scrollbar-track {
  background: transparent;
}

nav::-webkit-scrollbar-thumb {
  background: #E0E0E0;
  border-radius: 3px;
}

nav::-webkit-scrollbar-thumb:hover {
  background: #D1D5DB;
}
</style>
