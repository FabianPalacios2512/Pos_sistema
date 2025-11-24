---
applyTo: '**'
---

# 🎨 Sistema de Diseño - POS Empresarial Profesional

## 📋 Instrucciones Generales para IA

Cuando trabajes en este proyecto, SIEMPRE debes seguir este sistema de diseño para mantener la consistencia visual EMPRESARIAL y PROFESIONAL en todas las vistas.

---

## 🚀 FILOSOFÍA DE DISEÑO CRÍTICA - PANEL EMPRESARIAL

### ⚠️ IMPORTANTE: PRODUCTO COMERCIAL DE NIVEL EMPRESARIAL

**Este es un sistema POS empresarial que debe verse EXTREMADAMENTE PROFESIONAL, SERIO y CONFIABLE.**

### 🎨 **PALETA DE COLORES EMPRESARIAL**

---
todo los archivos de debug y test que creas los debes de eliminar 

siempre que necesites datos mira la base de datos, mysql se usuairo root y sin contraseña
---

Esta paleta está inspirada en aplicaciones empresariales modernas con colores suaves pero profesionales:

#### Colores Principales del Sistema:
```css
/* BOTONES PRIMARIOS - Verde Empresarial */
--primary-gradient: from-lime-400 to-green-400;
--primary-gradient-hover: from-lime-500 to-green-500;

/* BOTONES SECUNDARIOS - Azul Corporativo */  
--secondary-gradient: from-sky-500 to-blue-500;
--secondary-gradient-hover: from-sky-600 to-blue-600;

/* BOTONES NEUTROS - Slate Profesional */
--neutral-bg: bg-slate-50;
--neutral-hover: bg-slate-100;
--neutral-text: text-slate-600;
--neutral-border: border-slate-200;
```

#### Fondos de Métricas (Gradientes Suaves):
```css
/* Métricas de Éxito/Ingresos */
--metric-success-gradient: from-emerald-50 to-teal-50;
--metric-success-border: border-emerald-100;
--metric-success-text: text-emerald-600;
--metric-success-value: text-emerald-700;

/* Métricas de Rendimiento/Promedios */
--metric-performance-gradient: from-amber-50 to-orange-50;
--metric-performance-border: border-amber-100;
--metric-performance-text: text-amber-600;
--metric-performance-value: text-amber-700;

/* Métricas de Comparación/Tendencias */
--metric-comparison-gradient: from-sky-50 to-blue-50;
--metric-comparison-border: border-sky-100;
--metric-comparison-text: text-sky-600;
--metric-comparison-value: text-sky-700;

/* Métricas de Alertas/Importante */
--metric-alert-gradient: from-rose-50 to-red-50;
--metric-alert-border: border-rose-100;
--metric-alert-text: text-rose-600;
--metric-alert-value: text-rose-700;
```

#### Áreas de Contenido:
```css
/* Gráficos y Contenido Principal */
--chart-gradient: from-slate-50 to-blue-50;
--chart-border: border-slate-100;

/* Tarjetas Principales */
--card-bg: #FFFFFF;
--card-border: border-gray-200;
--card-shadow: shadow-sm;

/* Fondo de Página */
--page-bg: #F3F4F6; /* gray-100 - Fondo principal más claro y elegante */
```

---

## 🎯 REGLAS CRÍTICAS EMPRESARIALES (SIEMPRE APLICAR)

### ✅ **JERARQUÍA DE BOTONES EMPRESARIAL**

#### 1. Botones Primarios (Acciones Principales)
```html
<!-- ✅ CORRECTO: Botón de acción principal (Verde Empresarial) -->
<button class="px-5 py-2.5 bg-gradient-to-r from-lime-400 to-green-400 hover:from-lime-500 hover:to-green-500 text-white text-sm font-bold rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105 flex items-center space-x-2">
  <svg class="w-5 h-5">...</svg>
  <span>Nueva Venta</span>
</button>
```

#### 2. Botones Secundarios (Acciones Importantes)
```html
<!-- ✅ CORRECTO: Botón secundario (Azul Corporativo) -->
<button class="px-4 py-2.5 bg-gradient-to-r from-sky-500 to-blue-500 hover:from-sky-600 hover:to-blue-600 text-white rounded-xl text-sm font-semibold transition-all duration-200 flex items-center space-x-2">
  <svg class="w-4 h-4">...</svg>
  <span>Panel</span>
</button>
```

#### 3. Botones Neutros (Acciones de Soporte)
```html
<!-- ✅ CORRECTO: Botón neutro (Slate Profesional) -->
<button class="px-4 py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-600 text-sm font-semibold rounded-xl border border-slate-200 shadow-sm transition-all duration-200 flex items-center space-x-2 hover:shadow-md hover:border-slate-300">
  <svg class="w-4 h-4">...</svg>
  <span>Actualizar</span>
</button>
```

### ✅ **TARJETAS DE MÉTRICAS CON GRADIENTES EMPRESARIALES**

**USAR GRADIENTES SUAVES PARA MÉTRICAS PRINCIPALES:**

```html
<!-- ✅ CORRECTO: Métrica de Éxito/Ingresos (Verde Empresarial) -->
<div class="text-center p-3 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-lg border border-emerald-100 shadow-sm">
  <p class="text-xs text-emerald-600 font-medium mb-1">Total Período</p>
  <p class="text-lg font-bold text-emerald-700">$1,234,567</p>
</div>

<!-- ✅ CORRECTO: Métrica de Rendimiento (Amarillo/Naranja) -->
<div class="text-center p-3 bg-gradient-to-br from-amber-50 to-orange-50 rounded-lg border border-amber-100 shadow-sm">
  <p class="text-xs text-amber-600 font-medium mb-1">Promedio Diario</p>
  <p class="text-lg font-bold text-amber-700">$45k</p>
</div>

<!-- ✅ CORRECTO: Métrica de Comparación (Azul Empresarial) -->
<div class="text-center p-3 bg-gradient-to-br from-sky-50 to-blue-50 rounded-lg border border-sky-100 shadow-sm">
  <p class="text-xs text-sky-600 font-medium mb-1">vs. Anterior</p>
  <p class="text-lg font-bold text-sky-700">+15%</p>
</div>

<!-- ✅ CORRECTO: Métrica de Alert/Crítica (Rojo Suave) -->
<div class="text-center p-3 bg-gradient-to-br from-rose-50 to-red-50 rounded-lg border border-rose-100 shadow-sm">
  <p class="text-xs text-rose-600 font-medium mb-1">Stock Crítico</p>
  <p class="text-lg font-bold text-rose-700">5</p>
</div>
```

### ✅ **ÁREAS DE GRÁFICOS Y CONTENIDO PRINCIPAL**

```html
<!-- ✅ CORRECTO: Área de gráfico con gradiente empresarial -->
<div class="bg-gradient-to-br from-slate-50 to-blue-50 rounded-lg p-3 border border-slate-100 shadow-sm" style="height: 200px;">
  <!-- Gráfico aquí -->
</div>
```

---

## � Espaciado y Tamaños Empresariales (Compactos pero Elegantes)

### Padding y Márgenes
```css
/* Contenedor Principal */
--page-padding-mobile: 1rem;      /* p-4 */
--page-padding-desktop: 1.5rem;   /* lg:p-6 */

/* Espaciado entre secciones */
--section-spacing: 1rem;          /* space-y-4 */

/* Tarjetas */
--card-padding-small: 0.75rem;    /* p-3 (tarjetas de métricas) */
--card-padding-medium: 1rem;      /* p-4 (tarjetas de gráficos y contenido) */
--card-gap: 0.75rem;              /* gap-3 */
--card-gap-medium: 1rem;          /* gap-4 */
```

### Border Radius Empresarial
```css
--radius-small: 0.5rem;           /* rounded-lg (8px) - Tarjetas, botones */
--radius-xl: 0.75rem;             /* rounded-xl (12px) - Botones principales */
--radius-full: 9999px;            /* rounded-full - Badges, avatares */
```

### Sombras Profesionales
```css
--shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);           /* shadow-sm */
--shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);         /* shadow-md (hover) */
--shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);       /* shadow-lg (botones principales) */
```

---

## �🔤 Tipografía Empresarial

### Tamaños de Fuente
```css
/* Títulos de Página */
--text-page-title: 1.5rem;        /* text-2xl (24px) - font-bold */

/* Títulos de Sección */
--text-section-title: 1rem;       /* text-base (16px) - font-semibold */

/* Valores de Métricas */
--text-metric-value: 1.25rem;     /* text-xl (20px) - font-bold */

/* Labels y Descripciones */
--text-label: 0.75rem;            /* text-xs (12px) - font-medium */

/* Badges */
--text-badge: 0.75rem;            /* text-xs (12px) - font-semibold */

/* Botones Principales */
--text-btn-primary: 0.875rem;     /* text-sm (14px) - font-bold */

/* Botones Secundarios */
--text-btn-secondary: 0.875rem;   /* text-sm (14px) - font-semibold */
```

### Pesos de Fuente
```css
--font-regular: 400;              /* font-normal */
--font-medium: 500;               /* font-medium */
--font-semibold: 600;             /* font-semibold */
--font-bold: 700;                 /* font-bold */
```

---

## 🎯 REGLAS CRÍTICAS (SIEMPRE APLICAR)

### ✅ Tarjetas de Métricas Principales

**USAR FONDOS PÁLIDOS SOLO EN MÉTRICAS PRINCIPALES:**

```html
<!-- ✅ CORRECTO: Métrica de Éxito (Ventas) -->
<div class="rounded-lg shadow-sm border border-gray-200 p-3 hover:shadow-md transition-shadow duration-200" 
     style="background-color: #E6FFF1;">
  <div class="flex items-start justify-between mb-2">
    <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center">
      <svg class="w-5 h-5 text-green-600">...</svg>
    </div>
    <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
      Activo
    </span>
  </div>
  <div>
    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-0.5">Ventas Hoy</p>
    <p class="text-xl font-bold text-gray-900">$1,234,567</p>
    <p class="text-xs text-gray-500 mt-1.5">45 transacciones</p>
  </div>
</div>
```

**Especificaciones obligatorias:**
- Fondo: Color pálido según categoría con `style="background-color: #XXXXXX;"`
- Border: `border-gray-200`
- Padding: `p-3`
- Border radius: `rounded-lg`
- Sombra: `shadow-sm` normal, `hover:shadow-md`
- Iconos contenedor: `w-10 h-10 rounded-lg` con `bg-{color}-50`
- Iconos SVG: `w-5 h-5 text-{color}-600`
- Badge: `px-2 py-0.5 rounded-full` con colores vibrantes
- Labels: `text-xs font-medium text-gray-500 uppercase tracking-wide mb-0.5`
- Valores: `text-xl font-bold text-gray-900`
- Descripciones: `text-xs text-gray-500 mt-1.5`

### ✅ Tarjetas Grandes (Gráficos, Listas, Contenido)

**SIEMPRE USAR FONDO BLANCO:**

```html
<!-- ✅ CORRECTO: Tarjeta de contenido -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
  <h3 class="text-base font-semibold text-gray-900 mb-3">Título de Sección</h3>
  <!-- Contenido -->
</div>
```

**Especificaciones obligatorias:**
- Fondo: SIEMPRE `bg-white` (NO usar colores pálidos)
- Padding: `p-4`
- Títulos: `text-base font-semibold text-gray-900`
- Border radius: `rounded-lg`

---

## 🎯 Aplicación de Colores Empresarial por Categoría

### 🟢 Métricas de Éxito/Positivo (Verde Empresarial)
**Cuándo usar:** Ventas, ingresos, metas alcanzadas, estado activo, crecimiento
```html
<div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-lg border border-emerald-100 shadow-sm">
  <p class="text-emerald-600">Label</p>
  <p class="text-emerald-700 font-bold">Valor</p>
</div>

<!-- Botones principales -->
<button class="bg-gradient-to-r from-lime-400 to-green-400 hover:from-lime-500 hover:to-green-500">
  Acción Principal
</button>
```

### 🔵 Métricas de Información/Neutro (Azul Empresarial)
**Cuándo usar:** Productos vendidos, clientes, datos generales, estadísticas, tendencias
```html
<div class="bg-gradient-to-br from-sky-50 to-blue-50 rounded-lg border border-sky-100 shadow-sm">
  <p class="text-sky-600">Label</p>
  <p class="text-sky-700 font-bold">Valor</p>
</div>

<!-- Botones secundarios -->
<button class="bg-gradient-to-r from-sky-500 to-blue-500 hover:from-sky-600 hover:to-blue-600">
  Acción Secundaria
</button>
```

### 🟡 Métricas de Atención/Importante (Ámbar Empresarial)
**Cuándo usar:** Efectivo en caja, alertas importantes, acciones pendientes, promedios
```html
<div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-lg border border-amber-100 shadow-sm">
  <p class="text-amber-600">Label</p>
  <p class="text-amber-700 font-bold">Valor</p>
</div>
```

### 🔴 Métricas de Advertencia/Peligro (Rosa/Rojo Suave)
**Cuándo usar:** Stock crítico, errores, problemas, alertas urgentes
```html
<div class="bg-gradient-to-br from-rose-50 to-red-50 rounded-lg border border-rose-100 shadow-sm">
  <p class="text-rose-600">Label</p>
  <p class="text-rose-700 font-bold">Valor</p>
</div>
```

### ⚪ Elementos Neutros (Slate Profesional)
**Cuándo usar:** Botones de soporte, elementos de interfaz, backgrounds
```html
<!-- Botones neutros -->
<button class="bg-slate-50 hover:bg-slate-100 text-slate-600 border border-slate-200">
  Actualizar
</button>

<!-- Áreas de gráficos -->
<div class="bg-gradient-to-br from-slate-50 to-blue-50 border border-slate-100">
  Gráfico
</div>
```

---

## 📐 Layouts y Grids Empresariales

### Grid Principal de Métricas (4 columnas)
```html
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
  <!-- 4 tarjetas de métricas con gradientes empresariales -->
</div>
```

### Grid de Contenido (2/3 + 1/3)
```html
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
  <div class="lg:col-span-2">
    <!-- Gráfico o contenido principal -->
  </div>
  <div>
    <!-- Sidebar: Actividad, alertas, etc -->
  </div>
</div>
```

### Grid de 2 Columnas Iguales
```html
<div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
  <!-- 2 tarjetas iguales -->
</div>
```

---

## 🎨 Componentes Específicos Empresariales

### Botones

#### Botón Primario (Verde Empresarial)
```html
<button class="px-5 py-2.5 bg-gradient-to-r from-lime-400 to-green-400 hover:from-lime-500 hover:to-green-500 text-white text-sm font-bold rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105 flex items-center space-x-2">
  <svg class="w-5 h-5">...</svg>
  <span>Nueva Venta</span>
</button>
```

#### Botón Secundario (Azul Empresarial)
```html
<button class="px-4 py-2.5 bg-gradient-to-r from-sky-500 to-blue-500 hover:from-sky-600 hover:to-blue-600 text-white rounded-xl text-sm font-semibold transition-all duration-200 flex items-center space-x-2">
  <svg class="w-4 h-4">...</svg>
  <span>Panel</span>
</button>
```

#### Botón Neutro (Slate Profesional)
```html
<button class="px-4 py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-600 text-sm font-semibold rounded-xl border border-slate-200 shadow-sm transition-all duration-200 flex items-center space-x-2 hover:shadow-md hover:border-slate-300">
  <svg class="w-4 h-4">...</svg>
  <span>Actualizar</span>
</button>
```

### Badges de Estado Empresarial

```html
<!-- Éxito/Activo (Verde Empresarial) -->
<span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
  Activo
</span>

<!-- Advertencia (Rojo Suave) -->
<span class="px-2 py-0.5 bg-red-100 text-red-700 text-xs font-semibold rounded-full">
  12
</span>

<!-- Información (Azul Empresarial) -->
<span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
  35
</span>

<!-- Importante (Ámbar Empresarial) -->
<span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded-full">
  Pendiente
</span>
```

### Iconos en Tarjetas Empresariales

```html
<!-- Contenedor de icono con color empresarial -->
<div class="w-10 h-10 rounded-lg bg-{color}-50 flex items-center justify-center">
  <svg class="w-5 h-5 text-{color}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <!-- SVG path -->
  </svg>
</div>
```

**Tamaños de iconos empresariales:**
- Tarjetas métricas: Contenedor `w-10 h-10`, SVG `w-5 h-5`
- Headers de sección: SVG `w-4 h-4` o `w-3.5 h-3.5`
- Listas e items: SVG `w-3.5 h-3.5`
- Botones: SVG `w-4 h-4`

---

## ✅ Checklist de Implementación Empresarial

Al crear o modificar una vista, verifica:

- [ ] **Colores Empresariales:** Gradientes suaves en tarjetas de métricas principales
- [ ] **Tarjetas grandes:** Siempre con `bg-white` para contenido principal
- [ ] **Botones:** Verde empresarial para primarios, azul para secundarios, slate para neutros
- [ ] **Iconos:** Colores vibrantes (`text-{color}-600`) que contrasten profesionalmente
- [ ] **Badges:** `rounded-full` con colores empresariales (`bg-{color}-100 text-{color}-700`)
- [ ] **Padding:** `p-3` para métricas, `p-4` para contenido
- [ ] **Border radius:** `rounded-lg` (8px) consistente profesional
- [ ] **Sombras:** `shadow-sm` normal, `hover:shadow-md` para interactividad
- [ ] **Tipografía:** `text-xs` labels, `text-xl` valores, `text-base` títulos
- [ ] **Espaciado:** `space-y-4` entre secciones, `gap-3` en grids
- [ ] **Responsive:** `grid-cols-1 md:grid-cols-2 lg:grid-cols-4`
- [ ] **Transiciones:** `transition-shadow duration-200` en tarjetas
- [ ] **Contraste:** Texto legible sobre todos los fondos (ratio 4.5:1 mínimo)
- [ ] **Gradientes:** Usar gradientes empresariales apropiados por categoría

---

## 🎯 Ejemplos por Tipo de Vista

### Dashboard
```html
<!-- 4 Métricas principales CON fondos pálidos -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
  <div style="background-color: #E6FFF1;">...</div>  <!-- Verde -->
  <div style="background-color: #EBF2FF;">...</div>   <!-- Azul -->
  <div style="background-color: #FFF9E6;">...</div>   <!-- Amarillo -->
  <div style="background-color: #FFE6E6;">...</div>   <!-- Rojo -->
</div>

<!-- Gráficos y contenido SIN fondos pálidos -->
<div class="bg-white rounded-lg p-4">...</div>
```

### Listas (Productos, Clientes, etc)
```html
<!-- Tarjeta de lista SIEMPRE blanca -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
  <h3 class="text-base font-semibold text-gray-900 mb-3">Lista de Productos</h3>
  
  <!-- Items con hover -->
  <div class="space-y-2">
    <div class="p-2 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
      <!-- Contenido del item -->
    </div>
  </div>
</div>
```

### Formularios
```html
<!-- Formulario en tarjeta blanca -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
  <h3 class="text-base font-semibold text-gray-900 mb-3">Nuevo Producto</h3>
  
  <!-- Campos de formulario -->
  <div class="space-y-3">
    <div>
      <label class="block text-xs font-medium text-gray-700 mb-1">Nombre</label>
      <input class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
  </div>
</div>
```

---

## 🚫 Errores Comunes a Evitar

### ❌ NO hacer:
```html
<!-- ❌ INCORRECTO: Fondo de color en tarjeta grande -->
<div class="bg-blue-50 rounded-lg p-6">
  <h3>Gráfico de Ventas</h3>
  <!-- Contenido -->
</div>

<!-- ❌ INCORRECTO: Badge sin rounded-full -->
<span class="px-2 py-1 bg-green-100 text-green-700 rounded">Activo</span>

<!-- ❌ INCORRECTO: Iconos del mismo color que el fondo -->
<div class="w-10 h-10 bg-green-50">
  <svg class="text-green-50">...</svg>  <!-- ❌ No se verá -->
</div>

<!-- ❌ INCORRECTO: Usar bg-white en vez de style para métricas -->
<div class="bg-white">...</div>  <!-- ❌ Debería tener fondo pálido -->
```

### ✅ Hacer:
```html
<!-- ✅ CORRECTO: Fondo blanco en tarjeta grande -->
<div class="bg-white rounded-lg p-4">
  <h3 class="text-base font-semibold text-gray-900">Gráfico de Ventas</h3>
  <!-- Contenido -->
</div>

<!-- ✅ CORRECTO: Badge con rounded-full -->
<span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Activo</span>

<!-- ✅ CORRECTO: Iconos vibrantes que contrastan -->
<div class="w-10 h-10 bg-green-50">
  <svg class="text-green-600">...</svg>  <!-- ✅ Se ve claramente -->
</div>

<!-- ✅ CORRECTO: style inline para fondos pálidos -->
<div style="background-color: #E6FFF1;">...</div>
```

---

## 🔄 REGLAS CRÍTICAS PARA APLICAR EL DISEÑO EMPRESARIAL

### ⚠️ **1. LAYOUT PRINCIPAL (PosCompleto.vue) - SIEMPRE VERIFICAR:**

**UBICACIÓN:** `/src/views/PosCompleto.vue`

#### 📋 **Configuración Obligatoria del Layout Principal:**

```html
<!-- ✅ CORRECTO: Fondo principal SIEMPRE gris empresarial -->
<div class="min-h-screen bg-gray-100 dark:bg-gray-900">

<!-- ✅ CORRECTO: Header SIEMPRE blanco/gris claro -->
<header class="sticky top-0 z-40 bg-gray-50/95 shadow-md border-b border-gray-200 backdrop-blur-xl">

<!-- ✅ CORRECTO: Contenedores de módulos SIN padding ni fondo -->
<div v-if="currentModule === 'pos'">           <!-- ✅ Sin p-4 bg-white -->
<div v-if="currentModule !== 'dashboard'">     <!-- ✅ Sin p-4 bg-white -->
```

#### 🚨 **ERRORES COMUNES EN LAYOUT:**

```html
<!-- ❌ INCORRECTO: NO usar estas clases en contenedores de módulos -->
<div class="p-4 bg-white">  <!-- ❌ Causa padding blanco -->

<!-- ❌ INCORRECTO: NO cambiar el fondo principal -->
<div class="bg-white">      <!-- ❌ Debe ser bg-gray-200 -->
<div class="bg-gray-50">    <!-- ❌ Debe ser bg-gray-200 -->
```

### ⚠️ **2. ESTRUCTURA ESTÁNDAR DE VISTAS EMPRESARIALES:**

#### 📐 **Template Obligatorio para TODAS las Vistas:**

```html
<template>
  <div class="min-h-screen bg-gray-100 font-sans">
    <div class="p-4 lg:p-6 space-y-6 animate-fade-in">
      
      <!-- Header Simple y Elegante OBLIGATORIO -->
      <div class="flex items-center justify-between pb-4 border-b border-gray-300">
        <div class="flex items-center space-x-4">
          <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <!-- Icono específico del módulo -->
            </svg>
          </div>
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Título del Módulo</h1>
            <p class="text-sm text-gray-600">Descripción del sistema</p>
          </div>
        </div>
        
        <div class="flex items-center space-x-3">
          <!-- Botón Secundario (Neutro Slate) -->
          <button class="px-4 py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-600 text-sm font-semibold rounded-xl border border-slate-200 shadow-sm transition-all duration-200 flex items-center space-x-2 hover:shadow-md hover:border-slate-300">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <!-- Icono secundario -->
            </svg>
            <span>Acción Secundaria</span>
          </button>
          
          <!-- Botón Principal (Verde Empresarial) -->
          <button class="px-5 py-2.5 bg-gradient-to-r from-lime-400 to-green-400 hover:from-lime-500 hover:to-green-500 text-white text-sm font-bold rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <!-- Icono principal -->
            </svg>
            <span>Acción Principal</span>
          </button>
        </div>
      </div>
      
      <!-- Métricas Principales OBLIGATORIAS -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- 4 tarjetas con diseño empresarial -->
      </div>
      
      <!-- Contenido Principal -->
      <!-- Filtros, tablas, gráficos, etc. -->
      
    </div>
  </div>
</template>
```

#### 🎨 **Especificaciones del Header Elegante:**

```html
<!-- ✅ ESTRUCTURA OBLIGATORIA para header de módulos -->
<div class="flex items-center justify-between pb-4 border-b border-gray-300">
  <!-- Lado Izquierdo: Icono + Texto -->
  <div class="flex items-center space-x-4">
    <!-- Icono con gradiente azul empresarial -->
    <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
      <svg class="w-6 h-6 text-white"><!-- Icono específico --></svg>
    </div>
    <!-- Texto del módulo -->
    <div>
      <h1 class="text-2xl font-bold text-gray-900">Nombre del Módulo</h1>
      <p class="text-sm text-gray-600">Descripción breve</p>
    </div>
  </div>
  
  <!-- Lado Derecho: Botones de acción -->
  <div class="flex items-center space-x-3">
    <!-- Botón secundario (slate) + Botón principal (verde) -->
  </div>
</div>
```

### ⚠️ **3. DISEÑO ESTÁNDAR DE TARJETAS EMPRESARIALES:**

#### 🎯 **Tarjetas de Métricas Principales:**

```html
<!-- ✅ ESTRUCTURA OBLIGATORIA para métricas principales -->
<div class="bg-white rounded-2xl p-5 border border-gray-300 hover:border-gray-400 transition-all duration-200 hover:shadow-lg">
  <div class="flex items-center space-x-4">
    <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
      <svg class="w-6 h-6 text-gray-600"><!-- Icono --></svg>
    </div>
    <div class="flex-1 min-w-0">
      <div class="flex items-center justify-between mb-2">
        <h3 class="text-sm font-semibold text-gray-700">Título</h3>
        <span class="text-xs font-medium text-blue-700 bg-blue-50 px-2 py-1 rounded-lg border border-blue-200">
          Badge
        </span>
      </div>
      <p class="text-2xl font-bold text-gray-900 mb-1">Valor</p>
      <p class="text-sm text-gray-500">Descripción</p>
    </div>
  </div>
</div>
```

#### 🎯 **Tarjetas de Contenido (Tablas, Filtros):**

```html
<!-- ✅ ESTRUCTURA OBLIGATORIA para contenido principal -->
<div class="bg-white rounded-2xl p-5 border border-gray-300">
  <div class="bg-gray-50 border-b border-gray-200 px-5 py-4 flex items-center justify-between">
    <div>
      <h2 class="text-base font-bold text-gray-900">Título de Sección</h2>
      <p class="text-xs text-gray-500 mt-0.5">Descripción o estadísticas</p>
    </div>
  </div>
  <!-- Contenido -->
</div>
```

### ⚠️ **4. CHECKLIST OBLIGATORIO ANTES DE IMPLEMENTAR:**

- [ ] ✅ **PosCompleto.vue:** Fondo `bg-gray-100` + Header `bg-gray-50/95` + Sin `p-4 bg-white`
- [ ] ✅ **Vista Principal:** `min-h-screen bg-gray-100` + `p-4 lg:p-6 space-y-6`
- [ ] ✅ **Header Elegante:** `border-b border-gray-300` + Gradiente azul `from-blue-600 to-indigo-600`
- [ ] ✅ **Métricas:** `rounded-2xl p-5` + `flex items-center space-x-4` + `w-12 h-12 bg-gray-100 rounded-xl`
- [ ] ✅ **Espaciado:** `gap-6` en grids + `space-y-6` entre secciones
- [ ] ✅ **Botones:** Verde empresarial + Azul corporativo + Slate neutral
- [ ] ✅ **Badges:** `rounded-full` + colores empresariales apropiados

---

## 🎨 COMPARACIÓN: HEADER ANTIGUO vs NUEVO

### ❌ **Diseño Anterior (NO USAR):**
```html
<!-- ❌ ANTIGUO: Header con tarjeta blanca -->
<div class="bg-white rounded-2xl p-5 border border-gray-300 hover:shadow-lg transition-all duration-200">
  <div class="flex items-center justify-between">
    <div class="flex items-center space-x-3">
      <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
        <svg class="w-5 h-5 text-blue-600">...</svg>
      </div>
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Título</h1>
        <p class="text-sm text-gray-500 mt-1">Descripción</p>
      </div>
    </div>
  </div>
</div>
```

### ✅ **Diseño Nuevo (USAR SIEMPRE):**
```html
<!-- ✅ NUEVO: Header elegante con línea divisoria -->
<div class="flex items-center justify-between pb-4 border-b border-gray-300">
  <div class="flex items-center space-x-4">
    <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
      <svg class="w-6 h-6 text-white">...</svg>
    </div>
    <div>
      <h1 class="text-2xl font-bold text-gray-900">Título</h1>
      <p class="text-sm text-gray-600">Descripción</p>
    </div>
  </div>
  <div class="flex items-center space-x-3">
    <!-- Botones aquí -->
  </div>
</div>
```

### 🎯 **Diferencias Clave:**

| Elemento | Diseño Anterior ❌ | Diseño Nuevo ✅ |
|----------|-------------------|------------------|
| **Contenedor** | `bg-white rounded-2xl p-5 border` | `border-b border-gray-300 pb-4` |
| **Icono Tamaño** | `w-10 h-10` | `w-12 h-12` |
| **Icono Fondo** | `bg-blue-50 rounded-lg` | `bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl shadow-lg` |
| **Icono SVG** | `w-5 h-5 text-blue-600` | `w-6 h-6 text-white` |
| **Espaciado Izq** | `space-x-3` | `space-x-4` |
| **Subtítulo Color** | `text-gray-500 mt-1` | `text-gray-600` (sin mt-1) |
| **Botones Espaciado** | `space-x-2` | `space-x-3` |

---

## 🔄 Transiciones y Animaciones

### Transiciones Estándar
```css
/* Hover en tarjetas */
transition-shadow duration-200

/* Hover en botones primarios */
transition-all duration-300 transform hover:scale-[1.02]

/* Hover en botones secundarios */
transition-all duration-200

/* Cambios de estado general */
transition-colors duration-200
```

---

## 📱 Responsive Design

### Breakpoints
```css
/* Mobile first */
sm:  640px   /* Móvil horizontal */
md:  768px   /* Tablet */
lg:  1024px  /* Desktop */
xl:  1280px  /* Desktop grande */
```

### Grids Responsive
```html
<!-- 1 columna en móvil, 2 en tablet, 4 en desktop -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">

<!-- 1 columna en móvil, 3 columnas en desktop -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
```

---

## � DISEÑO DE TABLAS EN VISTAS DE GESTIÓN

**TODAS las vistas de gestión con tablas (Facturas, Inventario, Clientes, etc.) deben seguir este diseño:**

### ✅ Estructura de Tabla

```html
<!-- Contenedor de tabla -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
  <!-- Header de tabla -->si
  <div class="bg-gray-50 border-b border-gray-200 px-4 py-3 flex items-center justify-between">
    <div>
      <h2 class="text-base font-bold text-gray-900">Título de Sección</h2>
      <p class="text-xs text-gray-500 mt-0.5">Descripción o estadísticas</p>
    </div>
  </div>
  
  <!-- Tabla -->
  <table class="min-w-full divide-y divide-gray-200">
    <!-- Encabezados de columnas -->
    <thead class="bg-gray-50">
      <tr>
        <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">
          Columna 1
        </th>
        <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">
          Columna 2
        </th>
        <!-- Más columnas -->
      </tr>
    </thead>
    
    <!-- Filas de datos -->
    <tbody class="bg-white divide-y divide-gray-200">
      <tr class="hover:bg-gray-50 transition-colors">
        <td class="px-3 py-3">
          <!-- Contenido celda -->
        </td>
        <td class="px-3 py-3">
          <!-- Contenido celda -->
        </td>
      </tr>
    </tbody>
  </table>
</div>
```

### 🔍 Sección de Filtros (Papelería/Limpiar Filtros)

**TODAS las tablas deben incluir esta sección de filtros consistente:**

```html
<!-- Filtros compactos -->
<div class="bg-white rounded-lg shadow-sm p-3 border border-gray-200">
  <div class="flex flex-wrap items-center gap-3">
    <!-- Búsqueda -->
    <div class="flex-1 min-w-48 relative">
      <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400">
        <!-- Icono lupa -->
      </svg>
      <input type="text" 
             placeholder="Buscar..."
             class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
    </div>
    
    <!-- Select de filtro -->
    <select class="pl-3 pr-8 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 min-w-36">
      <option value="">Estado</option>
      <option value="opcion1">Opción 1</option>
    </select>
    
    <!-- Más filtros según necesidad -->
    
    <!-- Botón Limpiar Filtros (Papelería) -->
    <button @click="clearFilters" 
            class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
            title="Limpiar filtros">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
      </svg>
    </button>
  </div>
</div>
```

### 📄 PAGINADOR ESTÁNDAR (Obligatorio)

**TODOS los paginadores del sistema deben usar exactamente este diseño:**

```html
<!-- Paginador estándar -->
<div class="bg-white border-t border-gray-200 px-4 py-3 flex items-center justify-between">
  <!-- Información izquierda -->
  <div class="flex items-center space-x-3">
    <!-- Selector items por página -->
    <div class="flex items-center space-x-2">
      <span class="text-xs font-medium text-gray-700">Mostrar:</span>
      <select v-model="itemsPerPage" 
              @change="currentPage = 1"
              class="border border-gray-300 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
        <option value="10">10</option>
        <option value="25">25</option>
        <option value="50">50</option>
        <option value="100">100</option>
      </select>
      <span class="text-xs text-gray-700">por página</span>
    </div>
    
    <!-- Info de paginación -->
    <div class="text-xs text-gray-700">
      Mostrando {{ start }} a {{ end }} de {{ total }} registros
    </div>
  </div>
  
  <!-- Controles derecha -->
  <div class="flex items-center space-x-1">
    <!-- Primera página -->
    <button @click="currentPage = 1" 
            :disabled="currentPage === 1"
            class="p-1.5 text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
      </svg>
    </button>
    
    <!-- Anterior -->
    <button @click="currentPage--" 
            :disabled="currentPage === 1"
            class="p-1.5 text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
      </svg>
    </button>
    
    <!-- Números de página -->
    <div class="flex items-center space-x-1">
      <template v-for="page in totalPages" :key="page">
        <button v-if="page === 1 || page === totalPages || Math.abs(page - currentPage) <= 2"
                @click="currentPage = page"
                :class="[
                  'px-2.5 py-1 text-xs font-medium rounded-lg transition-colors',
                  page === currentPage 
                    ? 'bg-blue-600 text-white border border-blue-600' 
                    : 'text-gray-500 bg-white border border-gray-300 hover:bg-gray-50'
                ]">
          {{ page }}
        </button>
        <span v-else-if="Math.abs(page - currentPage) === 3" class="px-1 text-gray-400 text-xs">...</span>
      </template>
    </div>
    
    <!-- Siguiente -->
    <button @click="currentPage++" 
            :disabled="currentPage === totalPages"
            class="p-1.5 text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
      </svg>
    </button>
    
    <!-- Última página -->
    <button @click="currentPage = totalPages" 
            :disabled="currentPage === totalPages"
            class="p-1.5 text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
      </svg>
    </button>
  </div>
</div>
```

### 🎯 Reglas Críticas para Tablas

1. **Encabezado de tabla:** `bg-gray-50 border-b px-4 py-3`
2. **Columnas:** `px-3 py-2 text-xs font-bold text-gray-600 uppercase tracking-wide`
3. **Celdas:** `px-3 py-3` con contenido compacto
4. **Hover filas:** `hover:bg-gray-50 transition-colors`
5. **Badges estado:** `px-2 py-0.5 rounded-full text-xs font-semibold`
6. **Botones acción:** `p-1.5 bg-{color}-100 hover:bg-{color}-200 rounded-lg`
7. **Iconos acción:** `w-3.5 h-3.5`
8. **Sin gradientes, sin transforms, sin efectos pesados**

### 📦 Estado Vacío en Tablas

```html
<tr v-if="items.length === 0">
  <td colspan="7" class="px-4 py-12 text-center">
    <div class="flex flex-col items-center space-y-3">
      <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
        <svg class="w-6 h-6 text-gray-400"><!-- Icono --></svg>
      </div>
      <div>
        <p class="text-sm font-semibold text-gray-700">No hay datos</p>
        <p class="text-xs text-gray-500 mt-1">Mensaje descriptivo</p>
      </div>
      <button class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
        Acción
      </button>
    </div>
  </td>
</tr>
```

---

## 🎯 DISEÑO DE MODALES Y DIÁLOGOS

### Principios de Diseño para Modales

**TODOS los modales del sistema deben seguir esta filosofía de rediseño:**

1. **Layout Inteligente:**
   - Usar columnas cuando tenga sentido (ej: 2/3 contenido + 1/3 acciones)
   - Información del cliente/usuario en sidebar
   - Acciones de edición agrupadas visualmente
   - Integrar totales/resúmenes donde corresponda (no separados artificialmente)

2. **Headers Limpios:**
   - Sin gradientes, fondo blanco con borde
   - Icono: `w-10 h-10 bg-{color}-100 rounded-lg`
   - Título: `text-base font-bold`
   - Información contextual visible (cliente, fecha, estado)

3. **Footers con Jerarquía:**
   - Acciones destructivas (Eliminar) a la izquierda, separadas
   - Acciones principales (Cancelar, Guardar) a la derecha, juntas
   - Fondo `bg-gray-50` para separación visual
   - Botón primario con `shadow-sm`

4. **Responsive:**
   - `max-w-4xl` para modales estándar
   - `max-w-6xl` para modales con mucho contenido
   - Columnas se apilan en móviles automáticamente

### Ejemplo: Modal de Edición de Factura

```html
<!-- Layout de 2 columnas -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
  
  <!-- Contenido Principal (2/3) -->
  <div class="lg:col-span-2">
    <!-- Tabla con totales integrados en footer -->
    <div class="border-t bg-gray-50 px-3 py-3">
      <div class="flex justify-end">
        <div class="space-y-2">
          <div class="flex justify-between">
            <span>Subtotal:</span>
            <span class="font-semibold">$1,000</span>
          </div>
          <div class="border-t-2 pt-2">
            <div class="flex justify-between">
              <span class="font-bold">Total:</span>
              <span class="text-xl font-bold text-blue-600">$1,000</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Sidebar (1/3) -->
  <div class="space-y-4">
    <!-- Información -->
    <div class="bg-white border rounded-lg">
      <div class="bg-gray-50 border-b px-3 py-2">
        <h4 class="text-sm font-bold">Cliente</h4>
      </div>
      <div class="p-3">...</div>
    </div>
    
    <!-- Campos Editables -->
    <div class="bg-white border border-orange-200 rounded-lg">
      <div class="bg-orange-50 border-b border-orange-200 px-3 py-2">
        <h4 class="text-sm font-bold">Editar</h4>
      </div>
      <div class="p-3">...</div>
    </div>
  </div>
</div>

<!-- Footer -->
<div class="bg-gray-50 border-t px-4 py-3 flex justify-between">
  <button class="border border-red-300 text-red-600">Eliminar</button>
  <div class="flex gap-2">
    <button class="bg-white border">Cancelar</button>
    <button class="bg-blue-600 text-white shadow-sm">Guardar</button>
  </div>
</div>
```

---

## 📝 Notas Finales - Diseño Empresarial Profesional

1. **Consistencia Empresarial:** La clave del diseño profesional es la repetición de patrones y paletas corporativas
2. **Paleta Profesional:** Usar gradientes empresariales suaves en lugar de colores planos para dar elegancia
3. **Jerarquía Visual Corporativa:** Tamaños y pesos de fuente crean estructura empresarial clara
4. **Espaciado Compacto Profesional:** Mostrar más información sin saturar, manteniendo elegancia
5. **Accesibilidad Empresarial:** Contraste adecuado siempre (mínimo 4.5:1) para legibilidad profesional
6. **Performance Profesional:** Transiciones solo donde mejoran UX empresarial
7. **Paginador Empresarial:** Usar el componente TablePaginator.vue en todas las tablas
8. **Filtros Corporativos:** Incluir siempre el botón de limpiar filtros (papelería) en diseño limpio
9. **Rediseño Empresarial Completo:** No solo cambiar colores, mejorar layouts y UX para nivel corporativo
10. **Producto Comercial de Elite:** Cada vista debe ser SUPER PROFESIONAL, ELEGANTE y CONFIABLE como software empresarial de primer nivel

---

**Estas instrucciones son OBLIGATORIAS para mantener la consistencia del sistema empresarial profesional.**

**Última actualización:** 7 de noviembre de 2025  
**Versión:** 2.2 - Empresarial Profesional
