# 📄 Componente TablePaginator

Componente reutilizable para paginación consistente en todas las tablas del sistema.

## 🎯 Características

- ✅ Diseño compacto y profesional
- ✅ Selector de items por página (10, 25, 50, 100)
- ✅ Información de rango de registros
- ✅ Navegación completa (Primera, Anterior, Números, Siguiente, Última)
- ✅ Números de página con puntos suspensivos automáticos
- ✅ Estados disabled correctos
- ✅ Compatible con v-model
- ✅ Personalizable (label de registros)

## 📦 Importación

```javascript
import TablePaginator from '@/components/TablePaginator.vue';
```

## 🚀 Uso Básico

```vue
<template>
  <div>
    <!-- Tu tabla aquí -->
    <table>
      <!-- ... -->
    </table>
    
    <!-- Paginador -->
    <TablePaginator
      v-model:current-page="currentPage"
      v-model:items-per-page="itemsPerPage"
      :total-pages="totalPages"
      :total-items="totalItems"
      label="documentos"
    />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import TablePaginator from '@/components/TablePaginator.vue';

// Estado de paginación
const currentPage = ref(1);
const itemsPerPage = ref(10);
const items = ref([...]); // Tus datos

// Cálculos
const totalItems = computed(() => items.value.length);
const totalPages = computed(() => Math.ceil(totalItems.value / itemsPerPage.value));

// Items paginados
const paginatedItems = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  return items.value.slice(start, end);
});
</script>
```

## 🔧 Props

| Prop | Tipo | Requerido | Default | Descripción |
|------|------|-----------|---------|-------------|
| `currentPage` | `Number` | ✅ Sí | `1` | Página actual (usar con v-model:current-page) |
| `totalPages` | `Number` | ✅ Sí | `1` | Total de páginas disponibles |
| `itemsPerPage` | `Number` | ✅ Sí | `10` | Items por página (usar con v-model:items-per-page) |
| `totalItems` | `Number` | ✅ Sí | `0` | Total de items/registros |
| `itemsPerPageOptions` | `Array` | ❌ No | `[10, 25, 50, 100]` | Opciones para selector de items |
| `label` | `String` | ❌ No | `'registros'` | Etiqueta para el texto descriptivo |

## 📤 Eventos

| Evento | Payload | Descripción |
|--------|---------|-------------|
| `update:currentPage` | `Number` | Emitido cuando cambia la página actual |
| `update:itemsPerPage` | `Number` | Emitido cuando cambia items por página |

## 💡 Ejemplos de Uso

### Ejemplo 1: Facturas
```vue
<TablePaginator
  v-model:current-page="currentPage"
  v-model:items-per-page="itemsPerPage"
  :total-pages="totalPages"
  :total-items="filteredInvoices.length"
  label="documentos"
/>
```

### Ejemplo 2: Productos
```vue
<TablePaginator
  v-model:current-page="currentPage"
  v-model:items-per-page="itemsPerPage"
  :total-pages="totalPages"
  :total-items="filteredProducts.length"
  label="productos"
/>
```

### Ejemplo 3: Clientes
```vue
<TablePaginator
  v-model:current-page="currentPage"
  v-model:items-per-page="itemsPerPage"
  :total-pages="totalPages"
  :total-items="filteredCustomers.length"
  label="clientes"
/>
```

### Ejemplo 4: Opciones personalizadas
```vue
<TablePaginator
  v-model:current-page="currentPage"
  v-model:items-per-page="itemsPerPage"
  :total-pages="totalPages"
  :total-items="totalItems"
  :items-per-page-options="[5, 15, 30, 50]"
  label="transacciones"
/>
```

## 🎨 Diseño

El componente sigue el sistema de diseño estándar:

- **Tamaños:** Compactos con `text-xs`, `px-4 py-3`
- **Colores:** Grises suaves con azul para elementos activos
- **Iconos:** `w-3.5 h-3.5` para consistencia
- **Estados:** Hover y disabled bien definidos
- **Responsive:** Adaptable a diferentes tamaños

## 🔄 Lógica de Números de Página

El componente muestra automáticamente:
- Primera página (siempre)
- Última página (siempre)
- Páginas cercanas a la actual (±2 páginas)
- Puntos suspensivos "..." cuando hay saltos

**Ejemplo:**
- Si estás en página 1: `1 2 3 ... 10`
- Si estás en página 5: `1 ... 3 4 5 6 7 ... 10`
- Si estás en página 10: `1 ... 8 9 10`

## ⚡ Performance

- Computed properties para cálculos optimizados
- No re-renders innecesarios
- v-model para binding bidireccional eficiente

## 🎯 Uso Recomendado

1. **Importar una vez** en tu componente de vista
2. **Colocar al final** de la tabla (dentro o fuera del contenedor según diseño)
3. **Usar v-model** para sincronización automática
4. **Personalizar label** según el contexto (documentos, productos, clientes, etc.)
5. **Resetear currentPage a 1** cuando cambien los filtros

## 📝 Ejemplo Completo

```vue
<template>
  <div class="space-y-4">
    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow-sm p-3 border border-gray-200">
      <input 
        v-model="searchQuery" 
        @input="currentPage = 1"
        placeholder="Buscar..."
        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg"
      >
    </div>
    
    <!-- Tabla -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase">ID</th>
            <th class="px-3 py-2 text-left text-xs font-bold text-gray-600 uppercase">Nombre</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="item in paginatedItems" :key="item.id" class="hover:bg-gray-50">
            <td class="px-3 py-3 text-sm">{{ item.id }}</td>
            <td class="px-3 py-3 text-sm">{{ item.name }}</td>
          </tr>
        </tbody>
      </table>
      
      <!-- Paginador integrado -->
      <TablePaginator
        v-model:current-page="currentPage"
        v-model:items-per-page="itemsPerPage"
        :total-pages="totalPages"
        :total-items="filteredItems.length"
        label="items"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import TablePaginator from '@/components/TablePaginator.vue';

const items = ref([/* tus datos */]);
const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = ref(10);

// Filtrar items
const filteredItems = computed(() => {
  if (!searchQuery.value) return items.value;
  return items.value.filter(item => 
    item.name.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});

// Calcular paginación
const totalPages = computed(() => 
  Math.ceil(filteredItems.value.length / itemsPerPage.value)
);

const paginatedItems = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  return filteredItems.value.slice(start, end);
});
</script>
```

## ✅ Checklist de Implementación

Cuando uses el componente, asegúrate de:

- [ ] Importar el componente
- [ ] Definir `currentPage` y `itemsPerPage` como refs
- [ ] Calcular `totalPages` con computed
- [ ] Calcular `paginatedItems` con computed
- [ ] Usar v-model para binding bidireccional
- [ ] Resetear página a 1 cuando cambien filtros
- [ ] Personalizar label según contexto
- [ ] Mostrar solo los items paginados en la tabla

---

**Última actualización:** 7 de noviembre de 2025
