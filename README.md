# 🛒 105 POS - Sistema de Punto de Venta

Una plantilla completa de sistema POS (Punto de Venta) desarrollada con **Vue 3** y **TailwindCSS**, diseñada para tiendas físicas y minimarkets.

![Vue 3](https://img.shields.io/badge/Vue-3.3.4-4FC08D?style=for-the-badge&logo=vue.js)
![TailwindCSS](https://img.shields.io/badge/Tailwind-3.3.3-38B2AC?style=for-the-badge&logo=tailwind-css)
![Vite](https://img.shields.io/badge/Vite-4.4.9-646CFF?style=for-the-badge&logo=vite)

## ✨ Características Principales

### 🎨 Diseño Moderno y Responsivo
- **Interfaz limpia** con diseño profesional
- **Modo oscuro/claro** completo
- **Responsivo** para desktop, tablet y móvil
- **Animaciones suaves** y microinteracciones
- **Glassmorphism** y efectos visuales modernos

### 🛍️ Funcionalidades del POS
- **Catálogo de productos** con imágenes y precios
- **Búsqueda en tiempo real** por nombre o categoría
- **Filtros por categoría** (Snacks, Bebidas, Hogar, etc.)
- **Carrito de compras** dinámico con gestión de cantidades
- **Cálculo automático** de subtotales, IVA y total
- **Múltiples métodos de pago** (Efectivo, Tarjeta, Transferencia)

### 🧾 Sistema de Tickets
- **Generación de recibos** con formato profesional
- **Función de impresión** integrada
- **Datos completos** de la transacción
- **Diseño tipo ticket térmico**

### ⚙️ Configuración Avanzada
- **Personalización del negocio** (nombre, IVA, moneda)
- **Configuración de impuestos** (0%, 5%, 10%, 16%, 19%, 21%)
- **Múltiples monedas** disponibles
- **Persistencia en localStorage**

## 🚀 Instalación y Uso

### Prerrequisitos
- Node.js 16+ 
- npm o yarn

### 1. Clonar o descargar el proyecto
```bash
# Si tienes git
git clone [url-del-repositorio]
cd pos-tienda-demo

# O descargar y extraer el ZIP
```

### 2. Instalar dependencias
```bash
npm install
# o
yarn install
```

### 3. Ejecutar en desarrollo
```bash
npm run dev
# o 
yarn dev
```

### 4. Compilar para producción
```bash
npm run build
# o
yarn build
```

### 5. Vista previa de producción
```bash
npm run preview
# o
yarn preview
```

## 📱 Funcionalidades Detalladas

### Header Superior
- **Logo "105 POS"** con gradiente azul-púrpura
- **Fecha y hora en tiempo real** (actualización cada segundo)
- **Botones de acción**: Configuración, Modo oscuro, Cerrar sesión
- **Efecto glassmorphism** con backdrop-blur

### Catálogo de Productos (Columna Izquierda)
- **20 productos simulados** con imágenes de Unsplash
- **8 categorías**: Snacks, Bebidas, Hogar, Farmacia, Dulces, Panadería, Lácteos
- **Búsqueda instantánea** con ícono de lupa
- **Filtros por chips** de categoría
- **Tarjetas hover animadas** con efectos de escala
- **Botón "+" rápido** para agregar al carrito

### Panel de Venta (Columna Central)
- **Lista dinámica** de productos agregados
- **Controles de cantidad** (+ / -) por producto
- **Botón eliminar** individual por producto
- **Botón "Limpiar carrito"** general
- **Cálculos automáticos** en tiempo real
- **Animaciones slide-up** al agregar productos

### Panel de Cobro (Columna Derecha)
- **Resumen de totales**: Subtotal, IVA, Total
- **Selector de método de pago** con íconos
- **Botón "Finalizar Venta"** con gradiente
- **Estadísticas rápidas**: Productos y unidades
- **Diseño sticky** que se mantiene visible

### Modal de Ticket
- **Formato de recibo térmico** profesional
- **Datos completos** del negocio y transacción
- **Función de impresión** con window.print()
- **Animación scale-in** al abrir
- **Información detallada** por producto

### Modal de Configuración
- **Selector de tema** visual (claro/oscuro)
- **Nombre del negocio** personalizable
- **Tasa de IVA** configurable (0% - 21%)
- **Selección de moneda** (COP, USD, EUR, GBP, JPY)
- **Persistencia** en localStorage

## 🎨 Paleta de Colores

### Modo Claro
- **Fondo base**: `#f8fafc` (gray-50)
- **Acento azul**: `#2563eb` (blue-600)
- **Acento púrpura**: `#7c3aed` (violet-600)
- **Texto principal**: `#1e293b` (slate-800)

### Modo Oscuro
- **Fondo base**: `#111827` (gray-900)
- **Superficies**: `#1f2937` (gray-800)
- **Acentos**: Mismos colores con ajustes de opacidad
- **Texto**: `#f9fafb` (gray-50)

## 📁 Estructura del Proyecto

```
pos-tienda-demo/
├── index.html                 # Punto de entrada HTML
├── package.json               # Dependencias y scripts
├── vite.config.js            # Configuración de Vite
├── tailwind.config.js        # Configuración de Tailwind
├── postcss.config.js         # Configuración de PostCSS
└── src/
    ├── main.js               # Punto de entrada JS
    ├── App.vue               # Componente raíz
    ├── style.css             # Estilos globales y Tailwind
    └── views/
        └── PosTienda.vue     # 🎯 Componente principal del POS
```

## 🔧 Personalización

### Agregar Productos
Edita el array `products` en `/src/views/PosTienda.vue`:

```javascript
const products = [
  {
    id: 21,
    name: 'Nuevo Producto',
    category: 'Categoría',
    price: 5000,
    image: 'https://images.unsplash.com/photo-url'
  }
  // ...más productos
]
```

### Modificar Categorías
Actualiza el array `categories`:

```javascript
const categories = [
  'Todos',
  'Nueva Categoría',
  // ...otras categorías
]
```

### Cambiar Métodos de Pago
Edita `paymentMethods`:

```javascript
const paymentMethods = [
  { id: 'nuevo', name: 'Nuevo Método', icon: '💰' }
  // ...otros métodos
]
```

## 📱 Responsividad

### Desktop (1024px+)
- **Layout de 3 columnas** completo
- **Todos los paneles** visibles simultáneamente
- **Navegación completa** disponible

### Tablet (768px - 1023px)
- **Layout adaptativo** con columnas ajustables
- **Drawer lateral** para el carrito en móviles
- **Interfaz optimizada** para touch

### Móvil (< 768px)
- **Modo simplificado** de "cobro rápido"
- **Stack vertical** de componentes
- **Botones y controles** optimizados para dedos

## 🎯 Casos de Uso

### Como Demo para Clientes
- **Presentación visual** profesional
- **Simulación realista** de operaciones
- **Sin conexiones** externas requeridas
- **Funcionalidad completa** a nivel de interfaz

### Como Base para Desarrollo
- **Código limpio** y bien estructurado
- **Componentes modulares** fáciles de extender
- **Estilos organizados** con Tailwind
- **Arquitectura escalable** con Vue 3

### Para Aprendizaje
- **Ejemplo completo** de Vue 3 Composition API
- **Implementación avanzada** de TailwindCSS
- **Patrones de diseño** modernos
- **Gestión de estado** reactivo

## 🛠️ Tecnologías Utilizadas

- **Vue 3** - Framework JavaScript progresivo
- **Composition API** - API moderna de Vue 3
- **TailwindCSS** - Framework CSS utility-first
- **Vite** - Build tool rápido y moderno
- **Inter Font** - Tipografía profesional
- **Unsplash** - Imágenes de productos de alta calidad

## 📄 Licencia

Este proyecto es una plantilla de demostración desarrollada por **105 CODE** para fines educativos y de demostración.

## 🤝 Contribuciones

Las mejoras y sugerencias son bienvenidas. Este es un proyecto de plantilla diseñado para ser fácilmente personalizable y extensible.

---

**Desarrollado con ❤️ por 105 CODE**

*Sistema POS moderno y completo para tiendas físicas*