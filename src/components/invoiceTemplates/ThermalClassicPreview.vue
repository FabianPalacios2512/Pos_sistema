<template>
  <!-- 
    ✅ PREVIEW PROFESIONAL del ticket térmico CLASSIC
    Diseño tipo retail de lujo / supermercado corporativo
    Optimizado para impresión térmica 80mm
  -->
  <div class="ticket bg-white shadow-lg" style="width: 302px; margin: 0 auto;">
    
    <!-- ══════════════════════════════════════════════════════════════
         HEADER EMPRESA - Compacto y Profesional
    ══════════════════════════════════════════════════════════════ -->
    <div class="header text-center pt-4 pb-3 px-4">
      <!-- Logo (si existe) -->
      <div v-if="data.logo" class="flex justify-center mb-2">
        <img :src="data.logo" alt="Logo" class="logo-img">
      </div>
      
      <!-- Nombre de la tienda - DESTACADO -->
      <h1 class="store-name">
        {{ data.storeName || 'MI EMPRESA' }}
      </h1>
      
      <!-- Datos de contacto - Compactos -->
      <div class="company-info">
        <p v-if="data.nit">NIT: {{ data.nit }}</p>
        <p v-if="data.address">{{ data.address }}</p>
        <p v-if="data.phone">{{ data.phone }} <span v-if="data.email">• {{ data.email }}</span></p>
      </div>
    </div>

    <!-- Separador elegante -->
    <div class="divider-double mx-4"></div>

    <!-- ══════════════════════════════════════════════════════════════
         INFORMACIÓN DE FACTURA
    ══════════════════════════════════════════════════════════════ -->
    <div class="invoice-info mx-4 my-3">
      <p class="invoice-title">FACTURA DE VENTA</p>
      <p class="invoice-number">No. PRE-001</p>
    </div>

    <!-- Fecha, Hora, Atendido, Cliente -->
    <div class="meta-info mx-4 mb-3">
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

    <!-- Separador dashed -->
    <div class="divider-dashed mx-4"></div>

    <!-- Cliente -->
    <div class="customer-info mx-4 my-2">
      <span class="customer-label">CLIENTE:</span>
      <span class="customer-name">Cliente Final</span>
    </div>

    <!-- Separador doble antes de productos -->
    <div class="divider-double mx-4"></div>

    <!-- ══════════════════════════════════════════════════════════════
         TABLA DE PRODUCTOS - Layout Profesional
    ══════════════════════════════════════════════════════════════ -->
    <div class="products-section mx-4 my-3">
      <!-- Header de tabla -->
      <div class="products-header">
        <span class="col-desc">DESCRIPCIÓN</span>
        <span class="col-total">TOTAL</span>
      </div>

      <!-- Items -->
      <div class="products-list">
        <div v-for="(item, idx) in items" :key="idx" class="product-row">
          <div class="product-info">
            <span class="product-name">{{ item.name }}</span>
            <span class="product-detail">{{ item.quantity }} x ${{ formatNumber(item.price) }}</span>
          </div>
          <span class="product-total">${{ formatNumber(item.total) }}</span>
        </div>
      </div>
    </div>

    <!-- Separador dashed antes de totales -->
    <div class="divider-dashed mx-4"></div>

    <!-- ══════════════════════════════════════════════════════════════
         SECCIÓN DE TOTALES - Alineación Derecha Estricta
    ══════════════════════════════════════════════════════════════ -->
    <div class="totals-section mx-4 my-3">
      <div class="total-row">
        <span class="total-label">Subtotal:</span>
        <span class="total-value">${{ formatNumber(subtotal) }}</span>
      </div>
    </div>

    <!-- Separador doble antes del TOTAL FINAL -->
    <div class="divider-double mx-4"></div>

    <!-- TOTAL A PAGAR - Grande y Destacado -->
    <div class="grand-total mx-4 my-3">
      <span class="grand-total-label">TOTAL A PAGAR:</span>
      <span class="grand-total-value">${{ formatNumber(total) }}</span>
    </div>

    <!-- Separador dashed -->
    <div class="divider-dashed mx-4"></div>

    <!-- ══════════════════════════════════════════════════════════════
         FORMA DE PAGO
    ══════════════════════════════════════════════════════════════ -->
    <div class="payment-section mx-4 my-3">
      <p class="payment-title">FORMA DE PAGO</p>
      <div class="payment-row">
        <span class="payment-method">• Efectivo</span>
        <span class="payment-amount">${{ formatNumber(total) }}</span>
      </div>
    </div>

    <!-- Separador dashed -->
    <div class="divider-dashed mx-4"></div>

    <!-- ══════════════════════════════════════════════════════════════
         MENSAJE DE AGRADECIMIENTO
    ══════════════════════════════════════════════════════════════ -->
    <div class="thank-you-section text-center my-4 px-4">
      <p class="thank-you-message">
        {{ data.thankYouMessage || '¡Gracias por su compra!' }}
      </p>
    </div>

    <!-- ══════════════════════════════════════════════════════════════
         QR CODE
    ══════════════════════════════════════════════════════════════ -->
    <div class="qr-section text-center mb-3">
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
    <div class="legal-section text-center px-4 pb-2">
      <p>Régimen Común - No responsable de IVA</p>
      <p>Factura de venta Art. 617 del E.T.</p>
      <p>Resolución DIAN 18764069871234</p>
      <p>Vigencia: 01/01/2024 al 31/12/2024</p>
    </div>

    <!-- Separador final -->
    <div class="divider-thin mx-4 mb-2"></div>

    <!-- Powered by -->
    <div class="footer-powered text-center pb-4">
      <p>Powered by 105 POS</p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  data: {
    type: Object,
    required: true
  },
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
const formatTime = () => new Date().toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit', hour12: true })
</script>

<style scoped>
/* ═══════════════════════════════════════════════════════════════════
   TICKET TÉRMICO CLASSIC - DISEÑO PROFESIONAL CORPORATIVO
   Optimizado para impresión térmica 80mm (302px = 80mm aprox)
   Fondo blanco, texto 100% negro, sin grises para máximo contraste
═══════════════════════════════════════════════════════════════════ */

.ticket {
  font-family: 'Helvetica Neue', Arial, sans-serif;
  color: #000000;
  line-height: 1.3;
  max-width: 302px; /* ~80mm */
}

/* ─────────────────────────────────────────────────────────────────
   HEADER EMPRESA
───────────────────────────────────────────────────────────────── */
.logo-img {
  height: 40px;
  width: auto;
  max-width: 120px;
  object-fit: contain;
}

.store-name {
  font-size: 16px;
  font-weight: 800;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  color: #000000;
  margin: 0 0 4px 0;
}

.company-info {
  font-size: 10px;
  color: #000000;
  line-height: 1.4;
}

.company-info p {
  margin: 0;
}

/* ─────────────────────────────────────────────────────────────────
   SEPARADORES / DIVIDERS (CSS real, no caracteres)
───────────────────────────────────────────────────────────────── */
.divider-double {
  border-top: 1px solid #000000;
  padding-top: 1px;
  border-bottom: 1px solid #000000;
  height: 3px;
}

.divider-dashed {
  border-top: 1px dashed #000000;
}

.divider-thin {
  border-top: 1px solid #cccccc;
}

/* ─────────────────────────────────────────────────────────────────
   INFORMACIÓN DE FACTURA
───────────────────────────────────────────────────────────────── */
.invoice-info {
  text-align: center;
  padding: 8px 0;
}

.invoice-title {
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 1px;
  margin: 0 0 2px 0;
  color: #000000;
}

.invoice-number {
  font-size: 12px;
  font-weight: 800;
  font-family: 'Courier New', monospace;
  margin: 0;
  color: #000000;
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
  margin-bottom: 1px;
}

.meta-label {
  color: #000000;
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
  padding: 4px 0;
}

.customer-label {
  font-weight: 700;
  color: #000000;
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
  display: flex;
  justify-content: space-between;
  padding-bottom: 4px;
  border-bottom: 1px solid #000000;
  margin-bottom: 6px;
  font-weight: 700;
  font-size: 9px;
  letter-spacing: 0.5px;
}

.col-desc {
  text-align: left;
}

.col-total {
  text-align: right;
}

.products-list {
  /* Lista de productos */
}

.product-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding: 4px 0;
  border-bottom: 1px dotted #cccccc;
}

.product-row:last-child {
  border-bottom: none;
}

.product-info {
  display: flex;
  flex-direction: column;
  flex: 1;
  min-width: 0;
}

.product-name {
  font-weight: 600;
  color: #000000;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 180px;
}

.product-detail {
  font-size: 9px;
  color: #000000;
  font-family: 'Courier New', monospace;
  margin-top: 1px;
}

.product-total {
  font-weight: 700;
  font-family: 'Courier New', monospace;
  color: #000000;
  text-align: right;
  white-space: nowrap;
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
  color: #000000;
}

.total-value {
  font-family: 'Courier New', monospace;
  font-weight: 600;
  min-width: 80px;
  text-align: right;
}

/* ─────────────────────────────────────────────────────────────────
   TOTAL A PAGAR - DESTACADO
───────────────────────────────────────────────────────────────── */
.grand-total {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 0;
}

.grand-total-label {
  font-size: 12px;
  font-weight: 800;
  color: #000000;
  letter-spacing: 0.5px;
}

.grand-total-value {
  font-size: 20px;
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
  font-weight: 700;
  font-size: 9px;
  letter-spacing: 0.5px;
  margin: 0 0 4px 0;
  color: #000000;
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
  font-size: 12px;
  font-weight: 700;
  color: #000000;
  margin: 0;
}

/* ─────────────────────────────────────────────────────────────────
   QR CODE
───────────────────────────────────────────────────────────────── */
.qr-section {
  padding: 8px 0;
}

.qr-container {
  width: 64px;
  height: 64px;
  margin: 0 auto;
  border: 1px solid #000000;
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
  font-weight: 700;
  font-family: 'Courier New', monospace;
  margin: 6px 0 0 0;
  color: #000000;
}

/* ─────────────────────────────────────────────────────────────────
   INFORMACIÓN LEGAL
───────────────────────────────────────────────────────────────── */
.legal-section {
  font-size: 8px;
  color: #000000;
  line-height: 1.5;
}

.legal-section p {
  margin: 0;
}

/* ─────────────────────────────────────────────────────────────────
   FOOTER POWERED BY
───────────────────────────────────────────────────────────────── */
.footer-powered {
  font-size: 8px;
  color: #888888;
}

.footer-powered p {
  margin: 0;
}
</style>
