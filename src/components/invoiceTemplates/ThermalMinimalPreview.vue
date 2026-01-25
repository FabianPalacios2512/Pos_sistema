<template>
  <!-- 
    ✅ PREVIEW MINIMALISTA PURO del ticket térmico
    Diseño basado en tipografía, espacio en blanco y líneas sutiles
    Sin cajas, sin bordes gruesos - Verdadero minimalismo
  -->
  <div class="ticket bg-white shadow-lg" style="width: 302px; margin: 0 auto;">
    
    <!-- ══════════════════════════════════════════════════════════════
         HEADER EMPRESA - Tipografía pura, sin decoración
    ══════════════════════════════════════════════════════════════ -->
    <div class="header text-center pt-6 pb-4 px-4">
      <!-- Logo (si existe) - Simple, sin borde -->
      <div v-if="data.logo" class="flex justify-center mb-3">
        <img :src="data.logo" alt="Logo" class="logo-img">
      </div>
      
      <!-- Nombre de la tienda - Grande y Bold -->
      <h1 class="store-name">
        {{ data.storeName || 'MI EMPRESA' }}
      </h1>
      
      <!-- Datos de contacto - Limpios -->
      <div class="company-info">
        <p v-if="data.nit">NIT: {{ data.nit }}</p>
        <p v-if="data.address">{{ data.address }}</p>
        <p v-if="data.phone">{{ data.phone }} <span v-if="data.email">• {{ data.email }}</span></p>
      </div>
    </div>

    <!-- Separador fino -->
    <div class="divider mx-4"></div>

    <!-- ══════════════════════════════════════════════════════════════
         INFORMACIÓN DE FACTURA - Solo tipografía
    ══════════════════════════════════════════════════════════════ -->
    <div class="invoice-info text-center my-5 px-4">
      <p class="invoice-title">FACTURA DE VENTA</p>
      <p class="invoice-number">No. PRE-001</p>
    </div>

    <!-- Meta Info (Fecha, Hora, Atendido) -->
    <div class="meta-info mx-4 mb-4">
      <div class="meta-row">
        <span class="meta-label">Fecha:</span>
        <span class="meta-value">{{ formatDate() }}</span>
      </div>
      <div class="meta-row">
        <span class="meta-label">Hora:</span>
        <span class="meta-value">{{ formatTime() }}</span>
      </div>
      <div class="meta-row">
        <span class="meta-label">Atendido por:</span>
        <span class="meta-value">Vendedor</span>
      </div>
    </div>

    <!-- Separador fino -->
    <div class="divider mx-4"></div>

    <!-- Cliente -->
    <div class="customer-info mx-4 my-3">
      <span class="customer-label">CLIENTE:</span>
      <span class="customer-name">Cliente Final</span>
    </div>

    <!-- Separador fino -->
    <div class="divider mx-4"></div>

    <!-- ══════════════════════════════════════════════════════════════
         TABLA DE PRODUCTOS - Limpia y alineada
    ══════════════════════════════════════════════════════════════ -->
    <div class="products-section mx-4 my-4">
      <!-- Header de tabla -->
      <div class="products-header">
        <span class="col-desc">DESCRIPCIÓN</span>
        <span class="col-qty">CANT.</span>
        <span class="col-price">PRECIO</span>
        <span class="col-total">TOTAL</span>
      </div>

      <!-- Items -->
      <div class="products-list">
        <div v-for="(item, idx) in items" :key="idx" class="product-row">
          <span class="product-name">{{ item.name }}</span>
          <span class="product-qty">{{ item.quantity }}</span>
          <span class="product-price">${{ formatNumber(item.price) }}</span>
          <span class="product-total">${{ formatNumber(item.total) }}</span>
        </div>
      </div>
    </div>

    <!-- Separador fino -->
    <div class="divider mx-4"></div>

    <!-- ══════════════════════════════════════════════════════════════
         SECCIÓN DE TOTALES - Minimalista
    ══════════════════════════════════════════════════════════════ -->
    <div class="totals-section mx-4 my-4">
      <div class="total-row">
        <span class="total-label">Subtotal:</span>
        <span class="total-value">${{ formatNumber(subtotal) }}</span>
      </div>
    </div>

    <!-- Separador fino antes del TOTAL -->
    <div class="divider mx-4"></div>

    <!-- TOTAL A PAGAR - Grande, sin caja -->
    <div class="grand-total mx-4 my-5">
      <span class="grand-total-label">TOTAL A PAGAR</span>
      <span class="grand-total-value">${{ formatNumber(total) }}</span>
    </div>

    <!-- Separador fino -->
    <div class="divider mx-4"></div>

    <!-- ══════════════════════════════════════════════════════════════
         FORMA DE PAGO
    ══════════════════════════════════════════════════════════════ -->
    <div class="payment-section mx-4 my-4">
      <p class="payment-title">FORMA DE PAGO</p>
      <div class="payment-row">
        <span class="payment-method">• Efectivo</span>
        <span class="payment-amount">${{ formatNumber(total) }}</span>
      </div>
    </div>

    <!-- Separador fino -->
    <div class="divider mx-4"></div>

    <!-- ══════════════════════════════════════════════════════════════
         MENSAJE DE AGRADECIMIENTO
    ══════════════════════════════════════════════════════════════ -->
    <div class="thank-you-section text-center my-5 px-4">
      <p class="thank-you-message">
        {{ data.thankYouMessage || '¡Gracias por su compra!' }}
      </p>
    </div>

    <!-- ══════════════════════════════════════════════════════════════
         QR CODE - Simple, sin borde grueso
    ══════════════════════════════════════════════════════════════ -->
    <div class="qr-section text-center mb-4">
      <div class="qr-container">
        <svg class="qr-placeholder" fill="currentColor" viewBox="0 0 24 24">
          <path d="M3 3h8v8H3V3zm2 2v4h4V5H5zm8-2h8v8h-8V3zm2 2v4h4V5h-4zM3 13h8v8H3v-8zm2 2v4h4v-4H5zm13-2h3v2h-3v-2zm-5 0h2v3h-2v-3zm2 3h3v2h-3v-2zm0 2h2v3h-2v-3zm-2 0h2v2h-2v-2zm5-2h2v5h-2v-5zm-2 3h2v2h-2v-2z"/>
        </svg>
      </div>
      <p class="qr-code-number">PRE-001</p>
    </div>

    <!-- ══════════════════════════════════════════════════════════════
         PIE DE PÁGINA - INFORMACIÓN LEGAL
    ══════════════════════════════════════════════════════════════ -->
    <div class="legal-section text-center px-4 pb-3">
      <p>Régimen Común - No responsable de IVA</p>
      <p>Factura de venta Art. 617 del E.T.</p>
      <p>Resolución DIAN 18764069871234</p>
      <p>Vigencia: 01/01/2024 al 31/12/2024</p>
    </div>

    <!-- Separador final -->
    <div class="divider mx-4 mb-3"></div>

    <!-- Powered by -->
    <div class="footer-powered text-center pb-5">
      <p>Powered by 105 POS</p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  data: { type: Object, required: true },
  items: {
    type: Array,
    default: () => [
      { name: 'Producto A', quantity: 2, price: 25000, total: 50000 },
      { name: 'Producto B', quantity: 1, price: 100000, total: 100000 }
    ]
  }
})

const subtotal = computed(() => props.items.reduce((sum, item) => sum + item.total, 0))
const total = computed(() => subtotal.value)

const formatNumber = (value) => value.toLocaleString('es-CO')
const formatDate = () => new Date().toLocaleDateString('es-CO', { day: '2-digit', month: 'short', year: 'numeric' })
const formatTime = () => new Date().toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit', hour12: false })
</script>

<style scoped>
/* ═══════════════════════════════════════════════════════════════════
   TICKET TÉRMICO MINIMAL - DISEÑO MINIMALISTA PURO
   Basado en tipografía, espacio en blanco y líneas sutiles
   Sin cajas, sin bordes gruesos - Elegancia en la simplicidad
═══════════════════════════════════════════════════════════════════ */

.ticket {
  font-family: 'Helvetica Neue', 'Roboto', Arial, sans-serif;
  color: #000000;
  line-height: 1.5;
  max-width: 302px;
}

/* ─────────────────────────────────────────────────────────────────
   HEADER EMPRESA
───────────────────────────────────────────────────────────────── */
.logo-img {
  height: 40px;
  width: auto;
  max-width: 100px;
  object-fit: contain;
}

.store-name {
  font-size: 18px;
  font-weight: 700;
  letter-spacing: 2px;
  text-transform: uppercase;
  color: #000000;
  margin: 0 0 8px 0;
}

.company-info {
  font-size: 10px;
  color: #000000;
  line-height: 1.6;
}

.company-info p {
  margin: 0;
}

/* ─────────────────────────────────────────────────────────────────
   SEPARADOR - Línea fina y sutil
───────────────────────────────────────────────────────────────── */
.divider {
  border-top: 0.5px solid #cccccc;
}

/* ─────────────────────────────────────────────────────────────────
   INFORMACIÓN DE FACTURA - Solo tipografía
───────────────────────────────────────────────────────────────── */
.invoice-info {
  /* Espacio generoso */
}

.invoice-title {
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 2px;
  color: #000000;
  margin: 0 0 4px 0;
  text-transform: uppercase;
}

.invoice-number {
  font-size: 16px;
  font-weight: 800;
  font-family: 'Courier New', monospace;
  color: #000000;
  margin: 0;
}

/* ─────────────────────────────────────────────────────────────────
   META INFO (Fecha, Hora, Atendido)
───────────────────────────────────────────────────────────────── */
.meta-info {
  font-size: 10px;
}

.meta-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 2px;
}

.meta-label {
  color: #666666;
}

.meta-value {
  color: #000000;
  font-weight: 500;
}

/* ─────────────────────────────────────────────────────────────────
   CLIENTE
───────────────────────────────────────────────────────────────── */
.customer-info {
  display: flex;
  gap: 8px;
  font-size: 10px;
  padding: 6px 0;
}

.customer-label {
  font-weight: 600;
  color: #000000;
  font-size: 9px;
  letter-spacing: 1px;
}

.customer-name {
  color: #000000;
}

/* ─────────────────────────────────────────────────────────────────
   TABLA DE PRODUCTOS
───────────────────────────────────────────────────────────────── */
.products-section {
  font-size: 10px;
}

.products-header {
  display: grid;
  grid-template-columns: 1fr 36px 55px 55px;
  gap: 4px;
  padding: 8px 0;
  border-bottom: 0.5px solid #cccccc;
  margin-bottom: 8px;
  font-weight: 600;
  font-size: 8px;
  letter-spacing: 1px;
  color: #000000;
  text-transform: uppercase;
}

.col-desc {
  text-align: left;
}

.col-qty,
.col-price,
.col-total {
  text-align: right;
}

.products-list {
  /* Lista de productos */
}

.product-row {
  display: grid;
  grid-template-columns: 1fr 36px 55px 55px;
  gap: 4px;
  padding: 6px 0;
  border-bottom: 0.5px solid #eeeeee;
  align-items: center;
}

.product-row:last-child {
  border-bottom: none;
}

.product-name {
  text-align: left;
  color: #000000;
  font-weight: 500;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.product-qty {
  text-align: right;
  font-family: 'Courier New', monospace;
  color: #000000;
}

.product-price {
  text-align: right;
  font-family: 'Courier New', monospace;
  color: #666666;
  font-size: 9px;
}

.product-total {
  text-align: right;
  font-family: 'Courier New', monospace;
  font-weight: 700;
  color: #000000;
}

/* ─────────────────────────────────────────────────────────────────
   SECCIÓN DE TOTALES
───────────────────────────────────────────────────────────────── */
.totals-section {
  font-size: 11px;
}

.total-row {
  display: flex;
  justify-content: flex-end;
  gap: 20px;
}

.total-label {
  color: #666666;
}

.total-value {
  font-family: 'Courier New', monospace;
  font-weight: 600;
  color: #000000;
  min-width: 80px;
  text-align: right;
}

/* ─────────────────────────────────────────────────────────────────
   TOTAL A PAGAR - Grande, sin caja
───────────────────────────────────────────────────────────────── */
.grand-total {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  padding: 12px 0;
}

.grand-total-label {
  font-size: 12px;
  font-weight: 800;
  color: #000000;
  letter-spacing: 1px;
  text-transform: uppercase;
}

.grand-total-value {
  font-size: 24px;
  font-weight: 800;
  font-family: 'Courier New', monospace;
  color: #000000;
}

/* ─────────────────────────────────────────────────────────────────
   FORMA DE PAGO
───────────────────────────────────────────────────────────────── */
.payment-section {
  font-size: 10px;
}

.payment-title {
  font-weight: 600;
  font-size: 9px;
  letter-spacing: 1px;
  margin: 0 0 6px 0;
  color: #000000;
  text-transform: uppercase;
}

.payment-row {
  display: flex;
  justify-content: space-between;
}

.payment-method {
  color: #000000;
}

.payment-amount {
  font-family: 'Courier New', monospace;
  font-weight: 600;
  color: #000000;
}

/* ─────────────────────────────────────────────────────────────────
   MENSAJE DE AGRADECIMIENTO
───────────────────────────────────────────────────────────────── */
.thank-you-message {
  font-size: 13px;
  font-weight: 600;
  color: #000000;
  margin: 0;
}

/* ─────────────────────────────────────────────────────────────────
   QR CODE - Simple, sin borde grueso
───────────────────────────────────────────────────────────────── */
.qr-section {
  padding: 8px 0;
}

.qr-container {
  width: 60px;
  height: 60px;
  margin: 0 auto;
  padding: 4px;
  background: #ffffff;
}

.qr-placeholder {
  width: 100%;
  height: 100%;
  color: #000000;
}

.qr-code-number {
  font-size: 9px;
  font-weight: 600;
  font-family: 'Courier New', monospace;
  margin: 8px 0 0 0;
  color: #000000;
}

/* ─────────────────────────────────────────────────────────────────
   INFORMACIÓN LEGAL
───────────────────────────────────────────────────────────────── */
.legal-section {
  font-size: 8px;
  color: #888888;
  line-height: 1.6;
}

.legal-section p {
  margin: 0;
}

/* ─────────────────────────────────────────────────────────────────
   FOOTER POWERED BY
───────────────────────────────────────────────────────────────── */
.footer-powered {
  font-size: 8px;
  color: #aaaaaa;
}

.footer-powered p {
  margin: 0;
}
</style>
