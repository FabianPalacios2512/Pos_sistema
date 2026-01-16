# 🐌 → ⚡ Fix: LAG en Modales - Problema backdrop-blur

## 🔴 EL PROBLEMA

**Síntoma:**
- Modal se abre con lag severo
- Animación entrecortada/stuttering
- Sistema se siente lento y poco responsivo
- Peor en listas largas, tablas y modo oscuro

**Causa Raíz:**
```vue
<!-- ❌ ESTO CAUSA LAG -->
<div class="fixed inset-0 bg-black/70 backdrop-blur-sm ...">
```

El `backdrop-blur-sm` (o cualquier variante de blur) aplica un **filtro de desenfoque en tiempo real** a TODO el contenido detrás del modal.

**Por qué es tan costoso:**
- El navegador debe procesar CADA píxel del DOM subyacente
- Recalcula el efecto en cada frame de animación
- Con listas, tablas, gradientes o muchos elementos → GPU sufre
- Multiplica el trabajo de renderizado por 10x-50x

---

## ✅ LA SOLUCIÓN

**Eliminar `backdrop-blur-sm` del overlay:**

```vue
<!-- ✅ RÁPIDO Y FLUIDO -->
<div class="fixed inset-0 bg-black/70 dark:bg-black/85 flex items-center ...">
```

**Resultado:**
- ⚡ Modal se abre instantáneamente
- ⚡ Animación suave como mantequilla
- ⚡ Sin lag ni stuttering
- ✅ Fondo oscuro suficiente para contraste

---

## 📍 DÓNDE APLICAR

Buscar en todos los archivos Vue:

```bash
# Encontrar todos los modales con backdrop-blur
grep -r "backdrop-blur" src/components/*.vue
```

**Componentes comunes:**
- Modales de crear/editar (clientes, productos, usuarios)
- Modales de confirmación/alertas
- Sidebars/drawers con overlay
- Cualquier `<Teleport to="body">` con fondo difuminado

---

## 🎯 REGLA DE ORO

> **NUNCA uses `backdrop-blur` en overlays de modales.**  
> El fondo oscuro semitransparente (`bg-black/70`) es suficiente y 100x más rápido.

---

## 📝 EJEMPLO REAL CORREGIDO

**Antes (LAG):**
```vue
<Teleport to="body">
  <div v-if="showModal" 
       class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center">
    <!-- Modal content -->
  </div>
</Teleport>
```

**Después (RÁPIDO):**
```vue
<Teleport to="body">
  <div v-if="showModal" 
       class="fixed inset-0 bg-black/70 dark:bg-black/85 flex items-center justify-center">
    <!-- Modal content -->
  </div>
</Teleport>
```

---

## 🔧 APLICADO EN:

- [x] `CustomersView_clean.vue` - Modal crear/editar cliente (15/01/2026)

---

## 💡 NOTA TÉCNICA

Si el diseño **requiere** blur (muy raro), considera:
- Aplicar blur solo al contenido del modal, no al overlay
- Usar `backdrop-filter: blur(4px)` con mucho cuidado
- Limitar a áreas pequeñas (< 200x200px)
- Probar rendimiento en dispositivos lentos

**Pero 99% de los casos: simplemente NO lo uses.**
