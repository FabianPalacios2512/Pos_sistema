# 📏 Sistema de Unidades de Medida (UOM) - 105 POS Pro

## 📋 RESUMEN

Este documento explica cómo implementar el manejo de diferentes unidades de medida en el sistema POS, permitiendo ventas a granel con cantidades decimales.

---

## 🗄️ CAMBIOS EN BASE DE DATOS

### Nuevas Columnas en `products`:

```sql
-- measurement_unit: Unidad de medida del producto
ENUM('unit', 'kg', 'g', 'm', 'cm', 'l', 'ml') DEFAULT 'unit'

-- allow_decimal: ¿Permite cantidades decimales?
BOOLEAN DEFAULT false
```

### Ejemplos de Productos:

| Producto | measurement_unit | allow_decimal | Interpretación |
|----------|-----------------|---------------|----------------|
| iPhone 15 | unit | false | Se vende por unidades enteras (1, 2, 3) |
| Papas | kg | true | Se vende por peso (0.5 kg, 1.3 kg, etc) |
| Tela | m | true | Se vende por metros (2.5 m, 0.75 m, etc) |
| Gasolina | l | true | Se vende por litros (10.5 L, 25.8 L, etc) |

---

## 🔧 BACKEND (Laravel)

### 1. Ejecutar Migración:

```bash
php artisan migrate
```

### 2. Métodos Disponibles en el Modelo `Product`:

#### 📊 Accessors (Se agregan automáticamente al JSON):

```php
// Abreviatura de la unidad (und, kg, m, L, etc)
$product->unit_abbreviation

// Nombre completo de la unidad (Unidades, Kilogramos, Metros, etc)
$product->unit_name

// Paso para inputs numéricos ("1" o "0.001")
$product->quantity_step
```

#### ⚙️ Métodos Helper:

```php
// Validar si una cantidad es válida para este producto
$product->isValidQuantity(1.5) // true si permite decimales, false si no

// Calcular total con precisión (sin errores de redondeo)
$product->calculateTotal(1.5) // Retorna 3000.00 si precio es $2000/kg

// Formatear cantidad para mostrar
$product->formatQuantity(1.500) // Retorna "1.5" (quita ceros innecesarios)
```

#### 🔍 Query Scopes:

```php
// Filtrar por unidad de medida
Product::byMeasurementUnit('kg')->get()
Product::byMeasurementUnit(['kg', 'g'])->get()

// Solo productos que permiten decimales
Product::allowsDecimals()->get()
```

---

## 🎨 FRONTEND (Vue.js)

### 🚨 PROBLEMA: Errores de Redondeo en JavaScript

JavaScript usa punto flotante (IEEE 754), lo que causa errores:

```javascript
// ❌ INCORRECTO: Errores de precisión
0.1 + 0.2 === 0.30000000000000004  // true (WTF!)
2000 * 1.5 = 2999.9999999999995    // NO ES 3000!
```

### ✅ SOLUCIÓN: Trabajar en Centavos (Enteros)

```javascript
// 🎯 Función para calcular totales SIN errores de redondeo
const calculateLineTotal = (product, quantity) => {
  // Convertir precio a centavos (enteros)
  const priceInCents = Math.round(product.price * 100)
  
  // Convertir cantidad a "milésimas" para soportar 3 decimales (ej: 1.250 kg)
  const quantityInMillis = Math.round(quantity * 1000)
  
  // Multiplicar en enteros (sin decimales)
  const totalInCents = (priceInCents * quantityInMillis) / 1000
  
  // Convertir de vuelta a pesos con 2 decimales
  return Math.round(totalInCents) / 100
}

// Ejemplos:
calculateLineTotal({ price: 2000 }, 1.5)   // 3000.00 ✅
calculateLineTotal({ price: 2000 }, 0.5)   // 1000.00 ✅
calculateLineTotal({ price: 1500 }, 1.333) // 1999.50 ✅
```

### 📦 VALIDACIÓN DE CANTIDADES EN EL CARRITO

```javascript
const addToCart = (product, quantity) => {
  // Validar si el producto permite decimales
  if (!product.allow_decimal) {
    // Debe ser número entero
    if (quantity !== Math.floor(quantity)) {
      showError(`${product.name} solo se vende por unidades enteras`)
      return
    }
  }
  
  // Validar cantidad mínima
  if (quantity <= 0) {
    showError('La cantidad debe ser mayor a 0')
    return
  }
  
  // Calcular total con precisión
  const total = calculateLineTotal(product, quantity)
  
  // Agregar al carrito
  cart.push({
    product_id: product.id,
    name: product.name,
    quantity: quantity,
    price: product.price,
    subtotal: total,
    measurement_unit: product.measurement_unit,
    unit_abbreviation: product.unit_abbreviation
  })
}
```

### 🔢 INPUT NUMÉRICO EN FORMULARIOS

```vue
<template>
  <div>
    <label>Cantidad ({{ product.unit_abbreviation }})</label>
    <input 
      type="number" 
      v-model.number="quantity"
      :step="product.quantity_step"
      :min="product.allow_decimal ? '0.001' : '1'"
      @input="validateQuantity"
    />
  </div>
</template>

<script setup>
const validateQuantity = () => {
  if (!product.allow_decimal) {
    // Forzar entero
    quantity.value = Math.floor(quantity.value)
  } else {
    // Limitar a 3 decimales
    quantity.value = Math.round(quantity.value * 1000) / 1000
  }
}
</script>
```

### 💰 CÁLCULO DE TOTAL DEL CARRITO

```javascript
const cartTotal = computed(() => {
  // Sumar todos los subtotales (que ya están calculados con precisión)
  const sumInCents = cart.value.reduce((sum, item) => {
    return sum + Math.round(item.subtotal * 100)
  }, 0)
  
  // Convertir de vuelta a pesos
  return sumInCents / 100
})
```

### 🎨 MOSTRAR CANTIDAD EN LA UI

```vue
<template>
  <!-- Producto en el carrito -->
  <div class="cart-item">
    <span class="product-name">{{ item.name }}</span>
    <span class="quantity">
      {{ formatQuantity(item.quantity, item.product) }} {{ item.unit_abbreviation }}
    </span>
    <span class="price">${{ formatCurrency(item.subtotal) }}</span>
  </div>
</template>

<script setup>
const formatQuantity = (qty, product) => {
  if (!product.allow_decimal) {
    return qty.toFixed(0) // Sin decimales: "2"
  }
  
  // Con decimales: Quitar ceros innecesarios
  return parseFloat(qty.toFixed(3)).toString() // "1.5" en vez de "1.500"
}

const formatCurrency = (amount) => {
  return amount.toLocaleString('es-CO', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  })
}
</script>
```

---

## 🧪 CASOS DE PRUEBA

### Test 1: Producto por Unidades
```javascript
const iphone = {
  name: 'iPhone 15',
  price: 3500000,
  measurement_unit: 'unit',
  allow_decimal: false,
  unit_abbreviation: 'und'
}

addToCart(iphone, 2)   // ✅ OK: Total = $7,000,000
addToCart(iphone, 1.5) // ❌ ERROR: "Solo se vende por unidades enteras"
```

### Test 2: Producto por Peso
```javascript
const papas = {
  name: 'Papas',
  price: 2000,
  measurement_unit: 'kg',
  allow_decimal: true,
  unit_abbreviation: 'kg'
}

addToCart(papas, 1.5)   // ✅ OK: Total = $3,000
addToCart(papas, 0.750) // ✅ OK: Total = $1,500
addToCart(papas, 2.333) // ✅ OK: Total = $4,666
```

### Test 3: Producto por Metros
```javascript
const tela = {
  name: 'Tela Algodón',
  price: 15000,
  measurement_unit: 'm',
  allow_decimal: true,
  unit_abbreviation: 'm'
}

addToCart(tela, 2.5)   // ✅ OK: Total = $37,500
addToCart(tela, 0.25)  // ✅ OK: Total = $3,750
```

---

## 📝 CHECKLIST DE IMPLEMENTACIÓN

### Backend:
- [x] Crear migración `add_measurement_units_to_products`
- [x] Actualizar modelo `Product` con fillable y casts
- [x] Agregar accessors (`unit_abbreviation`, `unit_name`, `quantity_step`)
- [x] Agregar métodos helper (`calculateTotal`, `formatQuantity`, `isValidQuantity`)
- [x] Agregar query scopes (`byMeasurementUnit`, `allowsDecimals`)
- [ ] Ejecutar migración: `php artisan migrate`

### Frontend:
- [ ] Actualizar formulario de productos para incluir `measurement_unit` y `allow_decimal`
- [ ] Implementar función `calculateLineTotal()` para evitar errores de redondeo
- [ ] Actualizar input de cantidad en POS para soportar decimales (atributo `step`)
- [ ] Validar cantidades antes de agregar al carrito
- [ ] Mostrar unidad de medida en tarjetas de productos ("$2,000 / kg")
- [ ] Actualizar tabla de carrito para mostrar cantidad + unidad ("1.5 kg")
- [ ] Actualizar cálculo de total del carrito
- [ ] Probar casos extremos (0.001, 999.999, etc)

### API:
- [ ] Verificar que endpoints de productos incluyan `measurement_unit`, `allow_decimal`, `unit_abbreviation`
- [ ] Actualizar ProductController si es necesario para incluir campos en respuestas JSON

---

## 🎯 PRÓXIMOS PASOS RECOMENDADOS

1. **Ejecutar la migración** para agregar las columnas
2. **Actualizar el formulario de creación/edición de productos** para incluir selector de unidad de medida
3. **Modificar el POS** para:
   - Mostrar unidad en tarjeta de producto ("$2,000 / kg")
   - Input de cantidad con `step` dinámico
   - Validación de cantidad según `allow_decimal`
   - Cálculo de subtotal con precisión
4. **Probar exhaustivamente** con productos a granel antes de producción

---

## 🚀 IMPLEMENTACIÓN RÁPIDA EN 5 PASOS

```bash
# 1. Ejecutar migración
php artisan migrate

# 2. Verificar que funcionó
php artisan tinker
>>> Product::first()->unit_abbreviation
=> "und"

# 3. Crear producto de prueba
>>> $papas = Product::create([
...   'name' => 'Papas',
...   'price' => 2000,
...   'measurement_unit' => 'kg',
...   'allow_decimal' => true,
...   'sku' => 'PAP001',
...   'active' => true
... ])

# 4. Probar cálculos
>>> $papas->calculateTotal(1.5)
=> 3000.0

>>> $papas->unit_abbreviation
=> "kg"

>>> $papas->formatQuantity(1.500)
=> "1.5"

# 5. ¡Listo para implementar en frontend!
```

---

**Fecha:** 3 de diciembre de 2025  
**Sistema:** 105 POS Pro  
**Feature:** Unidades de Medida (UOM)  
**Estado:** ✅ Backend Completo - ⏳ Frontend Pendiente
