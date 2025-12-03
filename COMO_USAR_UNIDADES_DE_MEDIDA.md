# 📸 PREVIEW: Formulario Actualizado de Productos

## ✅ Campos que AHORA verás en el Modal:

### 1️⃣ Información Básica
- Nombre del Producto
- Categoría
- SKU
- Código de Barras
- Descripción

### 2️⃣ Precios e Inventario
- **Precio de Costo** (cuánto te cuesta)
- **Precio de Venta** (cuánto lo vendes) ← **Este es el precio por UNIDAD DE MEDIDA**
- Margen de Ganancia (automático)

### 3️⃣ ⭐ NUEVO: Unidad de Medida (Aparece aquí)
```
┌──────────────────────────────────────────────────────────┐
│ Unidad de Medida *                 Cantidad con Decimales│
│ (¿Cómo se vende?)                  (Auto-detectado)      │
│                                                           │
│ ┌────────────────────────────┐    ┌──────────────────┐  │
│ │ ⚖️ Kilogramos (kg)    [▼]  │    │ [✓] Sí (1.5 kg)  │  │
│ └────────────────────────────┘    └──────────────────┘  │
│ Seleccione cómo se vende          Permite 0.5, 1.25, etc│
│ este producto                                            │
└──────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────┐
│ 💰 Precio Final: $2,000 / kg                             │
│ Así se mostrará en el POS                                │
└──────────────────────────────────────────────────────────┘
```

### 4️⃣ Stock por Tienda
- Sede Principal: [100] kg
- Las Putas del Barrio: [50] kg
- **Stock Total: 150 kg**

---

## 📋 EJEMPLO REAL: Crear "Papas"

### Paso 1: Llenar Información
```
Nombre: Papas Criollas
Categoría: Verduras
SKU: PAP-001 (auto-generado)
```

### Paso 2: Precios
```
Precio de Costo: $ 1,500  (te cuesta $1,500 el kilo)
Precio de Venta: $ 2,000  (lo vendes a $2,000 el kilo)

📊 Margen: 25%
📊 Ganancia: $500 por kilo
```

### Paso 3: 🆕 Unidad de Medida
```
Unidad de Medida: [⚖️ Kilogramos (kg)]
Decimales: [✓] Sí (se activa automático)

💰 Precio Final: $2,000 / kg
```

### Paso 4: Stock
```
📦 Sede Principal: 100 kg
📦 Las Putas del Barrio: 50 kg
━━━━━━━━━━━━━━━━━━━━━━━━━━━
📊 Stock Total: 150 kg
```

---

## 🛒 ¿Cómo funciona en el POS?

Cuando crees el producto "Papas" con:
- **Precio de Venta: $2,000**
- **Unidad: kg**
- **Permite Decimales: Sí**

### En el POS se verá:
```
┌────────────────────────┐
│    Papas Criollas      │
│    $2,000 / kg         │ ← Precio POR KILO
│    Stock: 100 kg       │
└────────────────────────┘
```

### Al hacer clic:
1. **SI permite decimales (kg, m, l):**
   - ❌ NO va directo al carrito
   - ✅ Abre modal: "¿Cuántos kilos?"
   - Usuario escribe: `1.5`
   - Se agrega al carrito: `1.5 kg × $2,000 = $3,000`

2. **SI NO permite decimales (unidades):**
   - ✅ Va directo al carrito con cantidad = 1
   - Se agrega: `1 und × $50,000 = $50,000`

---

## 🎯 Opciones del Selector de Unidades:

```
🔢 Unidades (und)     → Para: iPhones, TVs, Laptops
⚖️ Kilogramos (kg)    → Para: Papas, Carne, Arroz
⚖️ Gramos (g)         → Para: Especias, Café molido
📏 Metros (m)         → Para: Tela, Cable, Tubería
📏 Centímetros (cm)   → Para: Cinta, Listón
🧴 Litros (L)         → Para: Gasolina, Aceite, Leche
🧴 Mililitros (ml)    → Para: Perfumes, Medicinas
```

---

## 🔧 Si NO ves los cambios:

### Solución 1: Recargar navegador forzado
- Windows/Linux: `CTRL + SHIFT + R`
- Mac: `CMD + SHIFT + R`

### Solución 2: Reiniciar Vite
```bash
# En la terminal donde corre npm:
CTRL + C
npm run dev
```

### Solución 3: Limpiar caché completo
```bash
# Detener todo
CTRL + C

# Limpiar y reiniciar
rm -rf node_modules/.vite
npm run dev
```

---

## ✅ Checklist de Verificación:

- [ ] Abrir navegador en el POS
- [ ] Presionar `CTRL + SHIFT + R` para refrescar sin caché
- [ ] Ir a "Productos" en el menú
- [ ] Click en "+ Nuevo Producto"
- [ ] **Buscar la sección "Unidad de Medida"** (después de "Margen de Ganancia")
- [ ] Seleccionar "Kilogramos (kg)"
- [ ] Verificar que el switch "Decimales" se active solo
- [ ] Poner precio de venta (ej: 2000)
- [ ] Ver el texto: "Precio Final: $2,000 / kg"
- [ ] Guardar producto
- [ ] Ir al POS y buscar el producto
- [ ] Click en el producto
- [ ] Debe abrir modal preguntando "¿Cuántos kg?"

---

**Si después de `CTRL + SHIFT + R` NO ves los campos, avísame y revisaremos juntos.** 🚀
