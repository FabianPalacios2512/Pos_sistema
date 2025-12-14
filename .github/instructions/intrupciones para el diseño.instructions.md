---
applyTo: '**'
---

# 🎨 Sistema de Diseño SaaS - POS Empresarial Profesional

## 🎯 Objetivo Principal

**Este es un sistema SaaS empresarial. TODO debe tener consistencia visual perfecta.**

Todas las vistas deben verse profesionales, modernas y cohesivas. No se permiten inconsistencias de color, spacing o estilo entre módulos diferentes.

---

## 🧠 Metodología de Trabajo Profesional

### ⚠️ REGLA CRÍTICA: Siempre Analizar Antes de Actuar

**PROCESO OBLIGATORIO para cualquier cambio:**

1. **📊 ANALIZAR primero:**
   - Revisar el código existente completo
   - Identificar patrones actuales de diseño
   - Verificar backend (endpoints, modelos, base de datos)
   - Revisar frontend (componentes, servicios, estados)
   - Comparar con otros módulos similares del sistema
   - Entender el flujo completo de datos

2. **🎯 IDENTIFICAR problemas:**
   - Listar inconsistencias visuales
   - Detectar datos faltantes o mal mapeados
   - Encontrar diferencias con el sistema de diseño
   - Verificar errores en modo claro/oscuro

3. **💡 PROPONER soluciones:**
   - Explicar qué se va a cambiar y por qué
   - Mostrar código "antes" y "después"
   - Justificar cada decisión de diseño

4. **✅ IMPLEMENTAR cambios:**
   - Aplicar modificaciones de forma ordenada
   - Probar consistencia visual
   - Validar que todo funcione correctamente

### ❌ NUNCA hacer cambios a ciegas

- NO modificar código sin antes revisarlo completamente
- NO asumir que los datos existen sin verificar
- NO aplicar estilos sin comparar con el resto del sistema
- NO ignorar el modo oscuro o el modo claro

### ✅ Ejemplo de Flujo Correcto

**Solicitud**: "Mejorar las imágenes de productos en inventario"

**Respuesta Correcta**:
```
1. ANALIZAR:
   - ✅ Revisar InventoryView_professional.vue
   - ✅ Verificar base de datos (productos con image_url)
   - ✅ Revisar backend (InventoryController, Product model)
   - ✅ Comparar con ProductsView para ver cómo maneja imágenes

2. IDENTIFICAR:
   - ❌ El mapeo no incluye image_url
   - ❌ Avatares con poco contraste en modo oscuro
   - ❌ Tamaño muy pequeño (w-10)

3. PROPONER:
   - ✅ Agregar image_url al mapeo de productos
   - ✅ Aumentar tamaño a w-12 h-12
   - ✅ Mejorar contraste: hsl(X, 80%, 35%)

4. IMPLEMENTAR:
   - [código con cambios específicos]
```

**Respuesta INCORRECTA** ❌:
```
"Voy a agregar las imágenes..."
[modifica código sin analizar]
```

---

## 🌈 Paleta de Colores SaaS (2024) - Gris Mate Profesional

### 🎯 Filosofía de Diseño: Work-Friendly & Eye-Comfort

Esta paleta está optimizada para **reducir la fatiga visual** en sesiones largas de trabajo.
Inspirада en **VS Code Dark+** y **GitHub Dark Dimmed**.

### Fondos con Gradientes

#### Modo Claro
```css
/* Fondo principal con gradiente suave */
bg-gradient-to-b from-gray-50 via-gray-100 to-gray-200
```

#### Modo Oscuro (Definitivo - Basado en Usuarios y Roles)
```css
/* Fondo principal - Gradiente oficial del sistema */
dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c]

/* ✅ ESTE ES EL GRADIENTE OFICIAL - NO CAMBIAR */
/* Probado y validado en: Usuarios y Roles, Gastos Operativos, Control de Cajas */
```

**⚠️ IMPORTANTE**: El gradiente debe aplicarse al contenedor principal (`min-h-screen`), NO a contenedores internos separados. Esto evita cortes visuales entre secciones.

**💡 Por qué este gradiente:**
- Balance perfecto entre elegancia y legibilidad
- Consistencia visual en todo el sistema
- Tonos slate profesionales que no cansan la vista
- Validado en producción con múltiples módulos

### Colores de Elemento

#### Paneles y Tarjetas
```css
/* Modo claro */
bg-white
border-gray-300
shadow-xl  /* Sombra fuerte para elevación */

/* Modo oscuro - Zinc 900 (Oficial) */
dark:bg-zinc-900  /* Superficie principal para paneles y tablas */
dark:border-zinc-800  /* Bordes sólidos */
dark:shadow-black/50  /* Sombra con buena profundidad */

/* Variantes semi-transparentes (glassmorphism) */
dark:bg-zinc-900/80  /* Para KPIs y cards flotantes con backdrop-blur-sm */
dark:bg-zinc-800  /* Para inputs de fecha/calendario */
dark:bg-zinc-800/50  /* Para hover en filas de tabla */
```

#### Botones Principales
```css
/* Acción principal (Slate oscuro) */
bg-slate-900 dark:bg-slate-700
hover:bg-black dark:hover:bg-slate-600
text-white
shadow-lg shadow-slate-400/40 dark:shadow-slate-900/50
transition-all duration-300
```

#### Botones Secundarios/Neutros
```css
/* Acciones secundarias */
bg-white dark:bg-[#252530]
hover:bg-slate-50 dark:hover:bg-[#2a2a35]
text-slate-600 dark:text-zinc-200
border-slate-200 dark:border-zinc-700/60
```

#### Botones de Acción (Iconos en Tablas)
```css
/* Editar (Amber) */
text-slate-400 dark:text-zinc-400
hover:text-amber-600 dark:hover:text-amber-400
hover:bg-amber-50 dark:hover:bg-amber-900/20
hover:border-amber-100 dark:hover:border-amber-900/30

/* Ver Detalles (Blue) */
text-slate-400 dark:text-zinc-400
hover:text-blue-600 dark:hover:text-blue-400
hover:bg-blue-50 dark:hover:bg-blue-900/20
hover:border-blue-100 dark:hover:border-blue-900/30

/* Eliminar/Desactivar (Rose) */
text-slate-400 dark:text-zinc-500
hover:text-rose-600 dark:hover:text-rose-400
hover:bg-rose-50 dark:hover:bg-rose-900/20
hover:border-rose-100 dark:hover:border-rose-900/30

/* Activar (Emerald) */
text-slate-400 dark:text-zinc-500
hover:text-emerald-600 dark:hover:text-emerald-400
hover:bg-emerald-50 dark:hover:bg-emerald-900/20
hover:border-emerald-100 dark:hover:border-emerald-900/30
```

---

## 🏗️ Estructura de Página SaaS

### Template Base Obligatorio

```vue
<template>
  <!-- Gradiente en TODO el fondo -->
  <div class="min-h-screen font-sans bg-gradient-to-b from-gray-50 via-gray-100 to-gray-200 dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c] transition-colors duration-300 px-8">
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
        <div class="bg-white dark:bg-[#1e1e24]/80 backdrop-blur-sm rounded-xl px-4 py-3 border border-gray-300 dark:border-zinc-800/60 hover:border-gray-400 dark:hover:border-zinc-700/80 transition-all duration-200 hover:shadow-md dark:shadow-lg dark:shadow-black/30">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-zinc-800/50 border border-white/5">
              <svg class="w-5 h-5 text-blue-400">...</svg>
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

### ✅ KPIs con Glassmorphism

**Efecto cristal obligatorio:**
```css
bg-white dark:bg-zinc-900/80
backdrop-blur-sm
border border-gray-300 dark:border dark:border-white/5
hover:border-gray-400 dark:hover:border-white/10
hover:shadow-md dark:shadow-lg dark:shadow-black/50
```

**Iconos con estilo Glass:**
- Contenedor: `w-12 h-12 rounded-xl` o `w-11 h-11` (según diseño)
- Fondo modo claro: `bg-gray-100` con `border-gray-200`
- Fondo modo oscuro: `dark:bg-zinc-800/50 dark:border-white/5`
- SVG: `w-5 h-5 text-{color}-600 dark:text-{color}-400`
- Ejemplo: `text-blue-600 dark:text-blue-400`

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
border-b border-gray-200 dark:border-zinc-800
/* Texto headers */
text-gray-600 dark:text-zinc-400
text-gray-700 dark:text-zinc-300  /* Para headers más prominentes */
```

**Tbody:**
```css
bg-white dark:bg-zinc-900
/* Mismo fondo que el contenedor */
```

**Filas con hover:**
```css
hover:bg-gray-50 dark:hover:bg-zinc-800/50
transition-colors duration-200
border-b border-gray-100 dark:border-zinc-800
```

**Badges de Estado (Pills):**
```css
/* Activo / Éxito */
bg-emerald-50 dark:bg-emerald-950
text-emerald-700 dark:text-emerald-400
border-emerald-100 dark:border-emerald-800

/* Inactivo / Error */
bg-rose-50 dark:bg-rose-950
text-rose-700 dark:text-rose-400
border-rose-100 dark:border-rose-800

/* Información / Primario */
bg-blue-50 dark:bg-blue-950
text-blue-700 dark:text-blue-400
border-blue-100 dark:border-blue-800

/* Advertencia */
bg-amber-50 dark:bg-amber-950
text-amber-700 dark:text-amber-400
border-amber-100 dark:border-amber-800

/* Morado / Especial */
bg-purple-50 dark:bg-purple-950
text-purple-700 dark:text-purple-400
border-purple-100 dark:border-purple-800

/* Estilo base */
px-2.5 py-1 rounded-full text-[10px] font-bold border uppercase tracking-wide
/* O para badges más grandes */
px-2 py-0.5 rounded-md text-xs font-medium border
```

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
bg-white dark:bg-zinc-900
text-gray-700 dark:text-zinc-300
font-medium
focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400
```

**Inputs de Fecha/Calendario:**
```css
px-3 py-3 text-sm
border border-gray-200 dark:border-zinc-700
bg-white dark:bg-zinc-800  /* Más claro para ver el calendario */
text-gray-700 dark:text-zinc-200
rounded-lg
focus:ring-2 focus:ring-blue-500 focus:border-blue-500
```

**Inputs de Texto en Modales:**
```css
px-3 py-2.5
border border-gray-300 dark:border-zinc-700
bg-white dark:bg-zinc-800
text-gray-900 dark:text-zinc-200
placeholder-gray-400 dark:placeholder-zinc-500
rounded-lg text-sm
focus:ring-2 focus:ring-blue-500 focus:border-blue-500
```

**Toggle de Vista (Tarjetas/Tabla):**
```css
/* Contenedor */
bg-gray-50 dark:bg-[#252530]
rounded-xl p-1
border border-gray-200 dark:border-zinc-700/60
h-[46px] /* Altura fija para coincidir con inputs */

/* Botón activo */
bg-white dark:bg-[#2a2a35]
text-gray-900 dark:text-white
shadow-sm

/* Botón inactivo */
text-gray-500 dark:text-zinc-400
hover:text-gray-900 dark:hover:text-white
```

**Botón Limpiar Filtros:**
```css
p-3
text-gray-500 dark:text-zinc-400
hover:text-red-600 dark:hover:text-red-400
hover:bg-red-50 dark:hover:bg-red-900/20
rounded-xl
border border-transparent hover:border-red-100 dark:hover:border-red-900/30
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
<div class="bg-gradient-to-b from-[#141417] to-black pb-8">
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
<div class="bg-gradient-to-b from-gray-50 via-gray-100 to-gray-200 dark:bg-gradient-to-b dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c]">
  <div class="p-4 lg:p-6 space-y-6">
    <!-- TODO el contenido -->
  </div>
</div>

<!-- ✅ Sin bordes en headers internos -->
<div class="flex items-center justify-between pb-4">

<!-- ✅ Sombras fuertes para elevación -->
shadow-xl dark:shadow-black/50

<!-- ✅ Tablas que contrastan -->
<div class="bg-white dark:bg-[#1e1e24] shadow-xl">

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
- [ ] Modo oscuro: `dark:from-[#141417] dark:via-slate-900 dark:to-[#0a0a0c]`
- [ ] Header sin icono, sin borde inferior
- [ ] KPIs con glassmorphism (`backdrop-blur-sm`, `border-white/5`)
- [ ] Iconos KPIs: `bg-gray-100 dark:bg-zinc-800/50` con `border-gray-200 dark:border-white/5`
- [ ] Botones principales: `bg-slate-900 dark:bg-slate-700`
- [ ] Paneles/tablas: `shadow-xl dark:shadow-black/50`
- [ ] Inputs búsqueda: `dark:bg-zinc-800` con `border-2`
- [ ] Selects: `dark:bg-zinc-900` con `py-3`
- [ ] Inputs fecha: `dark:bg-zinc-800` (más claro para ver calendario)
- [ ] Colores consistentes: `zinc-900`, `zinc-800`, `zinc-800/50`
- [ ] Texto: `dark:text-zinc-200`, `dark:text-zinc-300`, `dark:text-zinc-400`
- [ ] Texto blanco puro solo para títulos importantes
- [ ] Badges: usar variantes `-50/-950` con `-700/-400` en texto

---

## 📝 Notas para Desarrollo

### Base de Datos
- Usuario: `root`
- Contraseña: (vacía)
- Motor: MySQL

### Archivos de Debug
Eliminar todos los archivos de test y debug que se creen durante el desarrollo.

### Console.log y Debug
**IMPORTANTE**: Todos los `console.log()` agregados para debug durante el desarrollo DEBEN ser eliminados una vez solucionado el problema. El código en producción NO debe contener logs de debug.

**Excepciones permitidas**:
- Logs de error críticos (`console.error`)
- Logs de advertencia importantes (`console.warn`)
- Logs de información de sistema en desarrollo (con `if (import.meta.env.DEV)`)

**Nunca dejar**:
- `console.log()` de debug temporal
- `console.log()` con emojis de seguimiento (📊, ⚠️, ✅, etc.)
- Logs excesivos que contaminen la consola del navegador

### Consistencia SaaS
Este es un producto comercial de nivel empresarial. La consistencia visual es CRÍTICA. Todas las vistas deben verse como si fueran parte del mismo sistema - mismo spacing, mismos colores, misma jerarquía.
