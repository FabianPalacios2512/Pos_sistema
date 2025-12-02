# 🏢 Cómo Usar el Sistema Multi-Tienda

## 📝 **Tu Pregunta:**
> "Tengo un producto con 1 unidad en stock, pero quiero ponerle 20 unidades, pero NO en la bodega que tiene 1, sino en la OTRA bodega"

## ✅ **Solución:**

### **Paso 1: Ve a Catálogo de Productos**
En el menú lateral, haz clic en "Productos" para ver todos tus productos.

### **Paso 2: Encuentra tu Producto**
Busca el producto que tiene 1 unidad (por ejemplo: "Normal Cuadricualdo h100")

### **Paso 3: Haz Clic en Editar** ✏️
Haz clic en el icono de **editar** (lápiz) del producto que quieres modificar.

### **Paso 4: Verás la Nueva Sección "Stock por Tienda"** 🎯

Ahora verás algo como esto:

```
┌─────────────────────────────────────────────┐
│  Stock por Tienda *              [?]        │
├─────────────────────────────────────────────┤
│                                             │
│  📍 Sede Principal (Principal)              │
│  [____1____]  ← Tiene 1 unidad             │
│                                             │
│  📍 Las Margaritas                          │
│  [____0____]  ← Aquí pones 20              │
│                                             │
├─────────────────────────────────────────────┤
│  Stock Total: 1 unidades                    │
└─────────────────────────────────────────────┘
```

### **Paso 5: Modifica el Stock** ✨

**Opción A: Agregar stock a la otra tienda SIN modificar la primera**
- Deja "Sede Principal" en **1**
- Cambia "Las Margaritas" a **20**
- Stock Total = **21 unidades** (1 + 20)

**Opción B: Mover todo el stock a la otra tienda**
- Cambia "Sede Principal" a **0**
- Cambia "Las Margaritas" a **20**
- Stock Total = **20 unidades** (0 + 20)

**Opción C: Distribuir entre ambas**
- Cambia "Sede Principal" a **10**
- Cambia "Las Margaritas" a **20**
- Stock Total = **30 unidades** (10 + 20)

### **Paso 6: Guardar Cambios** 💾
Haz clic en el botón **"Actualizar Producto"** o **"Guardar"**

---

## 🎯 **Ejemplos Prácticos**

### Ejemplo 1: Mismo producto, stock diferente
```
Producto: "Cuaderno 100 hojas"

Stock Actual:
- Sede Principal: 1 unidad
- Las Margaritas: 0 unidades

Stock Nuevo que quieres:
- Sede Principal: 1 unidad (sin cambios)
- Las Margaritas: 20 unidades (nuevo)

Resultado Final:
✅ Sede Principal sigue teniendo 1 unidad
✅ Las Margaritas ahora tiene 20 unidades
✅ Stock Total: 21 unidades
```

### Ejemplo 2: Transferir todo a otra tienda
```
Producto: "Gorra Premium"

Stock Actual:
- Sede Principal: 5 unidades
- Las Margaritas: 0 unidades

Lo que haces:
- Sede Principal: 0 (dejas vacío)
- Las Margaritas: 50 (agregas nuevo stock)

Resultado:
✅ Sede Principal: 0 unidades (ya no se vende ahí)
✅ Las Margaritas: 50 unidades
✅ Stock Total: 50 unidades
```

### Ejemplo 3: Producto solo en algunas tiendas
```
Producto: "Producto Especial"

Configuración:
- Sede Principal: 100 unidades ✅
- Las Margaritas: 0 unidades ❌

Resultado:
✅ Solo se venderá en Sede Principal
❌ NO aparecerá en Las Margaritas (stock 0)
```

---

## 🔍 **¿Qué Pasa al Buscar Productos?**

### **Búsqueda Local** 📍
Cuando tienes activada la **búsqueda local**:
- Solo ves productos con stock en TU tienda actual
- Ejemplo: Si estás en "Sede Principal", solo ves productos con stock > 0 en Sede Principal

### **Búsqueda Global** 🌍
Cuando activas la **búsqueda global**:
- Ves TODOS los productos de TODAS las tiendas
- Te muestra en qué tiendas hay stock
- Ejemplo: "10 unidades en Sede Principal, 20 en Las Margaritas"

---

## ❓ **Preguntas Frecuentes**

### **¿Tengo que crear el mismo producto dos veces?**
**❌ NO.** Creas el producto UNA SOLA VEZ y asignas stock a cada tienda.

### **¿Puedo tener 0 stock en una tienda?**
**✅ SÍ.** Si pones 0, el producto NO estará disponible en esa tienda.

### **¿Se actualiza automáticamente cuando vendo?**
**✅ SÍ.** Cuando vendes en una tienda, solo se descuenta el stock de ESA tienda.

### **¿Puedo cambiar el stock después?**
**✅ SÍ.** Siempre puedes editar el producto y cambiar el stock de cualquier tienda.

---

## 🎨 **Interfaz Visual**

Cuando editas un producto, verás:

```
┌─────────────────────────────────────────────┐
│ Stock por Tienda *              [?] ← Ayuda │
├─────────────────────────────────────────────┤
│                                             │
│  💡 ¿Cómo funciona?                         │
│  • Puedes poner diferente stock en cada    │
│    tienda                                   │
│  • Ejemplo: 20 en Sede Principal, 5 en     │
│    Margaritas                               │
│  • Si dejas en 0, el producto NO estará    │
│    disponible en esa tienda                │
│                                             │
├─────────────────────────────────────────────┤
│                                             │
│  ┌─────────────────┬─────────────────┐    │
│  │ 📍 Sede         │ 📍 Las          │    │
│  │ Principal       │ Margaritas       │    │
│  │ (Principal)     │                  │    │
│  │                 │                  │    │
│  │ [____10___]     │ [____20___]     │    │
│  └─────────────────┴─────────────────┘    │
│                                             │
├─────────────────────────────────────────────┤
│  Stock Total: 30 unidades                   │
└─────────────────────────────────────────────┘
```

---

## ✅ **Resumen**

1. **Edita** el producto que quieres modificar
2. Verás **todos los inputs de stock por tienda**
3. **Modifica** el número en la tienda que necesitas
4. El **stock total** se calcula automáticamente
5. **Guarda** los cambios

**¡Listo! Ahora tienes control total del stock en cada tienda.** 🚀

---

**Fecha:** 30 de noviembre de 2025  
**Sistema:** POS Multi-Tienda v1.0
