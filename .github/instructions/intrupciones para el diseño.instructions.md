---
applyTo: '**'
---

# 🎨 Sistema de Diseño SaaS - POS Empresarial Profesional

## 🎯 Objetivo Principal

**Este es un sistema SaaS empresarial. TODO debe tener consistencia visual perfecta.**

Todas las vistas deben verse profesionales, modernas y cohesivas. No se permiten inconsistencias de color, spacing o estilo entre módulos diferentes.

---

## 🌈 Paleta de Colores SaaS (2024)

### Fondos con Gradientes

#### Modo Claro
```css
/* Fondo principal con gradiente suave */
bg-gradient-to-b from-gray-50 via-gray-100 to-gray-200
```

#### Modo Oscuro
```css
/* Fondo principal con gradiente dramático */
dark:bg-gradient-to-b dark:from-zinc-900 dark:via-zinc-950 dark:to-black
```

**⚠️ IMPORTANTE**: El gradiente debe aplicarse al contenedor principal (`min-h-screen`), NO a contenedores internos separados. Esto evita cortes visuales entre secciones.

### Colores de Elemento

#### Paneles y Tarjetas
```css
/* Modo claro */
bg-white
border-gray-300
shadow-xl  /* Sombra fuerte para elevación */

/* Modo oscuro */
dark:bg-zinc-900
dark:border-zinc-800
dark:shadow-black/50  /* Sombra negra pronunciada */
```

#### Botones Principales
```css
/* Acción principal (Slate oscuro) */
bg-slate-900 dark:bg-slate-700
hover:bg-black dark:hover:bg-slate-600
text-white
shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50
```

#### Botones Secundarios/Neutros
```css
/* Acciones secundarias */
bg-white dark:bg-zinc-900
hover:bg-slate-50 dark:hover:bg-zinc-800
text-slate-600 dark:text-zinc-200
border-slate-200 dark:border-zinc-800
```

---

## 🏗️ Estructura de Página SaaS

### Template Base Obligatorio

```vue
<template>
  <!-- Gradiente en TODO el fondo -->
  <div class="min-h-screen font-sans bg-gradient-to-b from-gray-50 via-gray-100 to-gray-200 dark:bg-gradient-to-b dark:from-zinc-900 dark:via-zinc-950 dark:to-black transition-colors duration-300 px-8">
    <div class="p-4 lg:p-6 space-y-6 pb-8 animate-fade-in">
      
      <!-- Header sin borde, sin contenedor separado -->
      <div class="flex items-center justify-between pb-4">
        <!-- Título sin icono -->
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Módulo</h1>
          <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">Descripción</p>
        </div>
        
        <!-- Botones -->
        <div class="flex items-center gap-3">
          <!-- Secundario -->
          <button class="px-5 py-2.5 bg-white dark:bg-zinc-900 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-600 dark:text-zinc-200 text-sm font-bold rounded-xl border border-slate-200 dark:border-zinc-800 shadow-sm transition-all duration-200">
            Refrescar
          </button>
          
          <!-- Principal -->
          <button class="px-6 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50 transition-all duration-300">
            Nueva Acción
          </button>
        </div>
      </div>

      <!-- KPIs con glassmorphism -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-zinc-900/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border dark:border-white/5 hover:border-gray-400 dark:hover:border-white/10 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/50">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 bg-blue-50 dark:bg-blue-950 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-blue-600 dark:text-blue-400">...</svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Label</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">Valor</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Contenido -->
    </div>
  </div>
</template>
```

---

## 🔑 Reglas Críticas de Diseño

### ✅ Fondos y Fluidez Visual

**SIEMPRE:**
- Aplicar gradiente al `<div>` principal, no a contenedores internos
- Usar `bg-gradient-to-b` para gradientes verticales
- NO crear contenedores separados con gradientes diferentes
- Evitar bordes visibles entre secciones (sin `border-b` en headers internos)

```vue
<!-- ✅ CORRECTO -->
<div class="min-h-screen bg-gradient-to-b from-gray-50 via-gray-100 to-gray-200 dark:bg-gradient-to-b dark:from-zinc-900 dark:via-zinc-950 dark:to-black">
  <div class="p-4 lg:p-6">
    <!-- Todo el contenido -->
  </div>
</div>

<!-- ❌ INCORRECTO - Crea cortes visuales -->
<div class="bg-zinc-950">
  <div class="bg-gradient-to-b from-zinc-900 to-black pb-8">
    <!-- Contenedor separado - SE VE FEO -->
  </div>
  <div class="p-4">
    <!-- Otro contenedor - CORTE VISIBLE -->
  </div>
</div>
```

### ✅ KPIs con Glassmorphism

**Efecto cristal obligatorio:**
```css
bg-white dark:bg-zinc-900/80
backdrop-blur-sm
border border-gray-300 dark:border-white/5
hover:border-gray-400 dark:hover:border-white/10
hover:shadow-md dark:shadow-lg dark:shadow-black/50
```

**Iconos:**
- Contenedor: `w-11 h-11` con `rounded-lg`
- Fondo: `bg-{color}-50 dark:bg-{color}-950`
- SVG: `w-5 h-5 text-{color}-600 dark:text-{color}-400`

**Texto:**
- Label: `text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide`
- Valor: `text-2xl font-bold text-gray-900 dark:text-white mt-0.5`

### ✅ Paneles y Tablas

**Contenedor principal:**
```css
bg-white dark:bg-zinc-900
rounded-2xl
shadow-xl dark:shadow-black/50
border border-gray-300 dark:border-zinc-800
```

**Header de tabla:**
```css
bg-gray-50 dark:bg-zinc-900
border-b border-gray-200 dark:border-zinc-800
```

**Thead:**
```css
bg-gray-50 dark:bg-zinc-900
border-b border-gray-200 dark:border-zinc-800
```

**Tbody:**
```css
bg-white dark:bg-zinc-900
divide-y divide-gray-200 dark:divide-zinc-800
```

**Filas con hover:**
```css
hover:bg-gray-50 dark:hover:bg-zinc-800/50
transition-all duration-200
border-b border-gray-100 dark:border-zinc-800
```

**Texto en filas:**
- Nombre: `text-sm font-medium text-gray-900 dark:text-zinc-200`
- Secundario: `text-xs text-gray-500 dark:text-zinc-400`

### ✅ Inputs y Filtros

**Input de búsqueda:**
```css
pl-10 pr-4 py-3 text-sm
rounded-xl border-2
border-gray-200 dark:border-zinc-700
bg-gray-50 dark:bg-zinc-800
text-gray-900 dark:text-zinc-200
placeholder-gray-400 dark:placeholder-zinc-500
focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400
focus:border-transparent
```

**Selects:**
```css
px-3 py-3 text-sm
rounded-lg border
border-gray-200 dark:border-zinc-700
bg-white dark:bg-zinc-800
text-gray-700 dark:text-zinc-300
font-medium
focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400
```

### ✅ Toggle de Vista (Tarjetas/Tabla)

```css
/* Contenedor */
bg-gray-50 dark:bg-zinc-800
rounded-lg p-1
border border-gray-200 dark:border-zinc-700

/* Botón activo */
bg-white dark:bg-zinc-900
text-gray-900 dark:text-white
shadow-sm

/* Botón inactivo */
text-gray-600 dark:text-zinc-400
hover:text-gray-900 dark:hover:text-white
```

---

## 🎨 Colores por Categoría

### KPIs de Éxito/Positivo
- Fondo icono: `bg-emerald-50 dark:bg-emerald-950`
- Icono: `text-emerald-600 dark:text-emerald-400`
- **Uso**: Ventas, ingresos, completados

### KPIs de Información
- Fondo icono: `bg-blue-50 dark:bg-blue-950`
- Icono: `text-blue-600 dark:text-blue-400`
- **Uso**: Total productos, facturas, datos generales

### KPIs de Atención
- Fondo icono: `bg-amber-50 dark:bg-amber-950`
- Icono: `text-amber-600 dark:text-amber-400`
- **Uso**: Stock bajo, pendientes, alertas

### KPIs Especiales
- Púrpura: `bg-purple-50 dark:bg-purple-950`, `text-purple-600 dark:text-purple-400`
- Índigo: `bg-indigo-50 dark:bg-indigo-950`, `text-indigo-600 dark:text-indigo-400`

### KPIs de Error/Crítico
- Fondo icono: `bg-red-50 dark:bg-red-950`
- Icono: `text-red-600 dark:text-red-400`
- **Uso**: Devoluciones, errores

---

## 🚫 Errores Comunes

### ❌ NO hacer:

```vue
<!-- ❌ Contenedores separados con gradientes -->
<div class="bg-gradient-to-b from-zinc-900 to-black pb-8">
  <div class="p-4">
    <!-- KPIs -->
  </div>
</div>
<div class="p-4">
  <!-- Tabla - SE VE CORTE -->
</div>

<!-- ❌ Bordes que rompen la fluidez -->
<div class="border-b border-gray-300">

<!-- ❌ Sombras débiles que no destacan -->
shadow-sm

<!-- ❌ Tablas del mismo color que el fondo -->
bg-zinc-900  <!-- cuando el fondo también es zinc-900 -->

<!-- ❌ Botones principales con verde -->
bg-gradient-to-r from-lime-400 to-green-400  <!-- OBSOLETO -->

<!-- ❌ Iconos en headers -->
<div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-indigo-600">
  <!-- Ya no se usan -->
</div>
```

### ✅ Hacer:

```vue
<!-- ✅ Un solo gradiente en todo el fondo -->
<div class="bg-gradient-to-b from-gray-50 via-gray-100 to-gray-200 dark:bg-gradient-to-b dark:from-zinc-900 dark:via-zinc-950 dark:to-black">
  <div class="p-4 lg:p-6 space-y-6">
    <!-- TODO el contenido -->
  </div>
</div>

<!-- ✅ Sin bordes en headers internos -->
<div class="flex items-center justify-between pb-4">

<!-- ✅ Sombras fuertes para elevación -->
shadow-xl dark:shadow-black/50

<!-- ✅ Tablas que contrastan -->
<div class="bg-white dark:bg-zinc-900 shadow-xl">

<!-- ✅ Botones principales con slate -->
<button class="bg-slate-900 dark:bg-slate-700 hover:bg-black">

<!-- ✅ Headers sin iconos -->
<div>
  <h1 class="text-2xl font-bold">Título</h1>
  <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">Descripción</p>
</div>
```

---

## ✅ Checklist de Implementación

Antes de dar por terminada una vista, verifica:

- [ ] Gradiente aplicado al contenedor principal (NO contenedores internos)
- [ ] Modo claro: `from-gray-50 via-gray-100 to-gray-200`
- [ ] Modo oscuro: `dark:from-zinc-900 dark:via-zinc-950 dark:to-black`
- [ ] Header sin icono, sin borde inferior
- [ ] KPIs con glassmorphism (`backdrop-blur-sm`, `border-white/5`)
- [ ] Botones principales: `bg-slate-900 dark:bg-slate-700`
- [ ] Paneles/tablas: `shadow-xl dark:shadow-black/50`
- [ ] Inputs: `border-2` con `py-3` para búsqueda
- [ ] Selects: `py-3` para altura consistente
- [ ] Colores consistentes: `zinc-900`, `zinc-800`, `gray-50`, etc.
- [ ] Texto: `dark:text-zinc-200`, `dark:text-zinc-400` para jerarquía
- [ ] Sin `zinc-850` u otros colores inexistentes en Tailwind

---

## 📝 Notas para Desarrollo

### Base de Datos
- Usuario: `root`
- Contraseña: (vacía)
- Motor: MySQL

### Archivos de Debug
Eliminar todos los archivos de test y debug que se creen durante el desarrollo.

### Consistencia SaaS
Este es un producto comercial de nivel empresarial. La consistencia visual es CRÍTICA. Todas las vistas deben verse como si fueran parte del mismo sistema - mismo spacing, mismos colores, misma jerarquía.
