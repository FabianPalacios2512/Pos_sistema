# 🛒 Guía de Integración Mercado Pago - 105 POS

## 📋 Resumen del Sistema Implementado

### Modelo de Precios Actual

**4 Planes Disponibles:**
1. **Trial 3 Días** - GRATIS (sin tarjeta)
2. **Emprendedor** - $65k/mes o $50k/mes anual
3. **Negocio Pro** - $65k/mes o $50k/mes anual (+ $15k DIAN opcional)
4. **Corporativo** - $65k/mes o $50k/mes anual

**Descuento Anual:** 23% de ahorro ($180k de descuento)
- Mensual: $65,000/mes
- Anual: $600,000/año ($50,000/mes)

---

## 🔑 PASO 1: Obtener Credenciales de Mercado Pago

### 1.1 Acceder a tu Panel de Mercado Pago

1. Ingresa a: https://www.mercadopago.com.co/developers
2. Inicia sesión con tu cuenta de Mercado Pago
3. Ve a **"Tus integraciones"** → **"Crear aplicación"**

### 1.2 Crear Aplicación

1. Haz clic en **"Crear aplicación"**
2. Nombre: `105 POS - SaaS Subscriptions`
3. Producto: **Checkout Pro** (pagos online)
4. Guarda la aplicación

### 1.3 Obtener Credenciales

En tu aplicación verás:

**Modo Test (Pruebas):**
```
Public Key Test: TEST-xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
Access Token Test: TEST-1234567890123456-123456-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-123456789
```

**Modo Producción (Real):**
```
Public Key: APP_USR-d1af5791-fa70-4707-a8b5-61b7bf81a978
Access Token: APP_USR-4051583343447871-120914-d1f45ea3071d39c9fdab5e3ba88985f6-3052668646
```

⚠️ **IMPORTANTE:** Comienza con credenciales de TEST primero.

---

## 🔧 PASO 2: Configuración Backend (Laravel)

### 2.1 Instalar SDK de Mercado Pago

```bash
cd backend
composer require mercadopago/dx-php
```

### 2.2 Agregar Credenciales al `.env`

Edita `backend/.env`:

```env
# Mercado Pago Configuration
MERCADOPAGO_PUBLIC_KEY=TEST-xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
MERCADOPAGO_ACCESS_TOKEN=TEST-1234567890123456-123456-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-123456789
MERCADOPAGO_WEBHOOK_SECRET=tu_secreto_para_webhooks_123
```

### 2.3 Crear Controlador de Pagos

**Archivo:** `backend/app/Http/Controllers/PaymentController.php`

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MercadoPago\SDK;
use MercadoPago\Preference;
use MercadoPago\Item;
use App\Models\Tenant;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct()
    {
        SDK::setAccessToken(env('MERCADOPAGO_ACCESS_TOKEN'));
    }

    /**
     * Crear preferencia de pago en Mercado Pago
     */
    public function createPreference(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string',
                'description' => 'required|string',
                'unit_price' => 'required|numeric|min:1000',
                'frequency' => 'required|in:monthly,yearly',
                'tenant_id' => 'required|string',
                'plan' => 'required|string',
                'include_dian' => 'boolean'
            ]);

            // Crear ítem de pago
            $item = new Item();
            $item->title = $validated['title'];
            $item->description = $validated['description'];
            $item->quantity = 1;
            $item->unit_price = $validated['unit_price'];
            $item->currency_id = "COP"; // Pesos colombianos

            // Crear preferencia
            $preference = new Preference();
            $preference->items = [$item];
            
            // URLs de retorno
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
            $preference->back_urls = [
                "success" => "{$frontendUrl}/payment/success",
                "failure" => "{$frontendUrl}/payment/failure",
                "pending" => "{$frontendUrl}/payment/pending"
            ];
            $preference->auto_return = "approved";

            // Metadata para identificar el pago
            $preference->external_reference = $validated['tenant_id'];
            $preference->metadata = [
                'tenant_id' => $validated['tenant_id'],
                'plan' => $validated['plan'],
                'frequency' => $validated['frequency'],
                'include_dian' => $validated['include_dian'] ?? false
            ];

            // Notificación por webhook
            $preference->notification_url = env('APP_URL') . "/api/mercadopago/webhook";

            // Guardar preferencia
            $preference->save();

            // Registrar transacción pendiente en BD
            PaymentTransaction::create([
                'tenant_id' => $validated['tenant_id'],
                'preference_id' => $preference->id,
                'plan' => $validated['plan'],
                'frequency' => $validated['frequency'],
                'amount' => $validated['unit_price'],
                'include_dian' => $validated['include_dian'] ?? false,
                'status' => 'pending',
                'metadata' => json_encode($validated)
            ]);

            Log::info('Preferencia de pago creada', [
                'preference_id' => $preference->id,
                'tenant_id' => $validated['tenant_id']
            ]);

            return response()->json([
                'success' => true,
                'preference_id' => $preference->id,
                'init_point' => $preference->init_point, // URL de checkout
                'sandbox_init_point' => $preference->sandbox_init_point // URL test
            ]);

        } catch (\Exception $e) {
            Log::error('Error creando preferencia MP', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Error al procesar el pago: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Webhook de Mercado Pago (notificaciones IPN)
     */
    public function webhook(Request $request)
    {
        try {
            Log::info('Webhook recibido de Mercado Pago', $request->all());

            // Verificar que sea notificación de pago
            if ($request->type !== 'payment') {
                return response()->json(['status' => 'ignored']);
            }

            $paymentId = $request->input('data.id');

            // Obtener información del pago
            $payment = \MercadoPago\Payment::find_by_id($paymentId);

            if (!$payment) {
                Log::error('Pago no encontrado', ['payment_id' => $paymentId]);
                return response()->json(['status' => 'error'], 404);
            }

            // Obtener tenant_id desde metadata
            $tenantId = $payment->external_reference;
            $metadata = $payment->metadata;

            Log::info('Pago procesado', [
                'payment_id' => $paymentId,
                'status' => $payment->status,
                'tenant_id' => $tenantId,
                'amount' => $payment->transaction_amount
            ]);

            // Actualizar transacción en BD
            $transaction = PaymentTransaction::where('preference_id', $payment->preference_id)->first();
            
            if ($transaction) {
                $transaction->update([
                    'payment_id' => $paymentId,
                    'status' => $payment->status,
                    'status_detail' => $payment->status_detail,
                    'payment_response' => json_encode($payment)
                ]);
            }

            // Si el pago fue aprobado, activar plan
            if ($payment->status === 'approved') {
                $tenant = Tenant::where('tenant_id', $tenantId)->first();
                
                if ($tenant) {
                    $plan = $metadata->plan ?? 'emprendedor';
                    $frequency = $metadata->frequency ?? 'monthly';
                    $includeDian = $metadata->include_dian ?? false;

                    // Calcular fecha de expiración
                    $expiresAt = $frequency === 'yearly' 
                        ? now()->addYear() 
                        : now()->addMonth();

                    $tenant->update([
                        'plan' => $plan,
                        'payment_frequency' => $frequency,
                        'include_dian' => $includeDian,
                        'plan_expires_at' => $expiresAt,
                        'status' => 'active',
                        'last_payment_at' => now()
                    ]);

                    Log::info('Plan activado', [
                        'tenant_id' => $tenantId,
                        'plan' => $plan,
                        'expires_at' => $expiresAt
                    ]);

                    // TODO: Enviar email de confirmación
                }
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Error procesando webhook MP', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Verificar estado de pago
     */
    public function checkPaymentStatus(Request $request)
    {
        try {
            $paymentId = $request->input('payment_id');
            $payment = \MercadoPago\Payment::find_by_id($paymentId);

            return response()->json([
                'success' => true,
                'status' => $payment->status,
                'status_detail' => $payment->status_detail,
                'amount' => $payment->transaction_amount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
```

### 2.4 Crear Modelo y Migración para Transacciones

**Migración:** `backend/database/migrations/2024_01_XX_create_payment_transactions_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->string('preference_id')->nullable();
            $table->string('payment_id')->nullable()->index();
            $table->string('plan'); // emprendedor, negocio_pro, enterprise
            $table->string('frequency'); // monthly, yearly
            $table->decimal('amount', 12, 2);
            $table->boolean('include_dian')->default(false);
            $table->string('status')->default('pending'); // pending, approved, rejected, cancelled
            $table->string('status_detail')->nullable();
            $table->json('metadata')->nullable();
            $table->json('payment_response')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('tenant_id')->on('tenants')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_transactions');
    }
};
```

**Modelo:** `backend/app/Models/PaymentTransaction.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'tenant_id',
        'preference_id',
        'payment_id',
        'plan',
        'frequency',
        'amount',
        'include_dian',
        'status',
        'status_detail',
        'metadata',
        'payment_response'
    ];

    protected $casts = [
        'include_dian' => 'boolean',
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'payment_response' => 'array'
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'tenant_id');
    }
}
```

### 2.5 Actualizar Modelo Tenant

Agregar a `backend/app/Models/Tenant.php`:

```php
protected $fillable = [
    // ... existentes
    'payment_frequency',
    'include_dian',
    'plan_expires_at',
    'last_payment_at'
];

protected $casts = [
    // ... existentes
    'include_dian' => 'boolean',
    'plan_expires_at' => 'datetime',
    'last_payment_at' => 'datetime'
];

public function paymentTransactions()
{
    return $this->hasMany(PaymentTransaction::class, 'tenant_id', 'tenant_id');
}
```

### 2.6 Agregar Rutas

**Archivo:** `backend/routes/api.php`

```php
use App\Http\Controllers\PaymentController;

// Rutas de pago
Route::post('/create-payment-preference', [PaymentController::class, 'createPreference']);
Route::post('/mercadopago/webhook', [PaymentController::class, 'webhook']);
Route::get('/check-payment-status', [PaymentController::class, 'checkPaymentStatus']);
```

### 2.7 Ejecutar Migraciones

```bash
cd backend
php artisan migrate
```

---

## 🎨 PASO 3: Integración Frontend (Vue.js)

### 3.1 Actualizar `handlePlanSelection` en `SaasRegister.vue`

Ya está implementado, pero asegúrate de que llame al endpoint correcto:

```javascript
const handlePlanSelection = async (plan) => {
  // ... código existente ...

  if (confirmed) {
    try {
      // Crear preferencia de pago en Mercado Pago
      const apiUrl = import.meta.env.VITE_API_URL ? `${import.meta.env.VITE_API_URL}/api` : '/api'
      
      const response = await axios.post(`${apiUrl}/create-payment-preference`, paymentData)
      
      if (response.data.success) {
        // Redirigir al checkout de Mercado Pago
        const checkoutUrl = import.meta.env.VITE_APP_ENV === 'production' 
          ? response.data.init_point 
          : response.data.sandbox_init_point
        
        // Guardar tenant_id en localStorage para recuperarlo después del pago
        localStorage.setItem('pending_tenant_id', tenantCreated.value.tenant_id)
        localStorage.setItem('pending_plan', plan)
        
        window.location.href = checkoutUrl
      } else {
        throw new Error(response.data.error || 'Error al crear preferencia de pago')
      }
      
    } catch (error) {
      console.error('Error en pago:', error)
      alert('❌ Error al procesar el pago\n\n' + (error.response?.data?.error || error.message))
    }
  }
}
```

### 3.2 Crear Vistas de Resultado de Pago

**Archivo:** `src/views/PaymentSuccess.vue`

```vue
<template>
  <div class="min-h-screen bg-gradient-to-b from-gray-50 to-white flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl border border-gray-200 p-8 text-center">
      <!-- Icono de éxito -->
      <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
      </div>

      <h1 class="text-3xl font-bold text-gray-900 mb-4">¡Pago Exitoso! 🎉</h1>
      
      <p class="text-gray-600 mb-6">
        Tu suscripción ha sido activada correctamente. En unos segundos serás redirigido a tu panel de control.
      </p>

      <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4 mb-6">
        <p class="text-sm text-emerald-800">
          <strong>Plan:</strong> {{ planName }}<br>
          <strong>Estado:</strong> Activo ✅
        </p>
      </div>

      <button 
        @click="redirectToDashboard"
        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 rounded-lg transition-colors"
      >
        Ir a mi Panel
      </button>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()
const planName = ref('')

onMounted(async () => {
  // Obtener información del pago
  const paymentId = route.query.payment_id
  const tenantId = localStorage.getItem('pending_tenant_id')
  const plan = localStorage.getItem('pending_plan')
  
  planName.value = plan === 'emprendedor' ? 'Emprendedor' : 
                   plan === 'negocio_pro' ? 'Negocio Pro' : 'Corporativo'

  // Redirigir después de 3 segundos
  setTimeout(() => {
    redirectToDashboard()
  }, 3000)
})

const redirectToDashboard = () => {
  const tenantId = localStorage.getItem('pending_tenant_id')
  localStorage.removeItem('pending_tenant_id')
  localStorage.removeItem('pending_plan')
  
  // Redirigir al dashboard del tenant
  window.location.href = `/tenant/${tenantId}/dashboard`
}
</script>
```

**Archivo:** `src/views/PaymentFailure.vue`

```vue
<template>
  <div class="min-h-screen bg-gradient-to-b from-gray-50 to-white flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl border border-gray-200 p-8 text-center">
      <!-- Icono de error -->
      <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </div>

      <h1 class="text-3xl font-bold text-gray-900 mb-4">Pago Rechazado</h1>
      
      <p class="text-gray-600 mb-6">
        No se pudo procesar tu pago. Por favor verifica tu información e intenta nuevamente.
      </p>

      <div class="space-y-3">
        <button 
          @click="retryPayment"
          class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition-colors"
        >
          Intentar Nuevamente
        </button>

        <button 
          @click="activateTrial"
          class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 rounded-lg transition-colors"
        >
          Activar Trial de 3 Días
        </button>
      </div>

      <a href="https://wa.me/573001234567" class="block mt-4 text-sm text-blue-600 hover:underline">
        ¿Necesitas ayuda? Contáctanos por WhatsApp
      </a>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()

const retryPayment = () => {
  router.push('/register?step=3')
}

const activateTrial = () => {
  // Activar trial de 3 días
  router.push('/register?step=3&plan=trial_3_days')
}
</script>
```

### 3.3 Agregar Rutas en Vue Router

**Archivo:** `src/router/index.js`

```javascript
{
  path: '/payment/success',
  name: 'PaymentSuccess',
  component: () => import('../views/PaymentSuccess.vue')
},
{
  path: '/payment/failure',
  name: 'PaymentFailure',
  component: () => import('../views/PaymentFailure.vue')
},
{
  path: '/payment/pending',
  name: 'PaymentPending',
  component: () => import('../views/PaymentPending.vue')
}
```

---

## 🔒 PASO 4: Seguridad y Webhooks

### 4.1 Configurar Webhook en Mercado Pago

1. Ve a tu aplicación en https://www.mercadopago.com.co/developers
2. En **"Webhooks"** → **"Configurar notificaciones"**
3. URL de producción: `https://tudominio.com/api/mercadopago/webhook`
4. Eventos: Selecciona **"Pagos"**
5. Guarda

### 4.2 Verificar Firma del Webhook (Opcional pero Recomendado)

En `PaymentController.php`:

```php
private function verifyWebhookSignature(Request $request)
{
    $xSignature = $request->header('x-signature');
    $xRequestId = $request->header('x-request-id');
    
    // Extraer timestamp y hash
    preg_match('/ts=(\d+),v1=([a-f0-9]+)/', $xSignature, $matches);
    $timestamp = $matches[1] ?? null;
    $hash = $matches[2] ?? null;
    
    if (!$timestamp || !$hash) {
        return false;
    }
    
    // Construir mensaje
    $dataId = $request->input('data.id');
    $message = "id:{$dataId};request-id:{$xRequestId};ts:{$timestamp};";
    
    // Verificar hash
    $secret = env('MERCADOPAGO_WEBHOOK_SECRET');
    $calculatedHash = hash_hmac('sha256', $message, $secret);
    
    return hash_equals($calculatedHash, $hash);
}
```

---

## 📊 PASO 5: Testing y Validación

### 5.1 Tarjetas de Prueba de Mercado Pago

**Tarjetas Aprobadas:**
```
Número: 5031 7557 3453 0604
CVV: 123
Fecha: 11/25
Nombre: APRO (Approved)
```

**Tarjetas Rechazadas:**
```
Número: 5031 7557 3453 0604
CVV: 123
Fecha: 11/25
Nombre: OTHE (Other reason)
```

### 5.2 Flujo de Testing Completo

1. **Registro de Tenant:**
   - Llena formulario de registro
   - Crea cuenta de prueba

2. **Selección de Plan:**
   - Elige plan (Emprendedor, Negocio Pro o Corporativo)
   - Selecciona frecuencia (Mensual o Anual)
   - Marca DIAN si aplica

3. **Redirección a Mercado Pago:**
   - Verifica que se cree la preferencia
   - Confirma redirección al checkout

4. **Pago en Mercado Pago:**
   - Usa tarjeta de prueba
   - Completa el pago

5. **Retorno y Activación:**
   - Verifica redirección a `/payment/success`
   - Confirma que el webhook activó el plan
   - Verifica fecha de expiración en BD

### 5.3 Logs y Debug

Revisar logs de Laravel:

```bash
tail -f backend/storage/logs/laravel.log
```

Buscar:
- `Preferencia de pago creada`
- `Webhook recibido de Mercado Pago`
- `Plan activado`

---

## 🚀 PASO 6: Ir a Producción

### 6.1 Cambiar a Credenciales Reales

1. En Mercado Pago, copia credenciales de **Producción**
2. Actualiza `backend/.env`:

```env
MERCADOPAGO_PUBLIC_KEY=APP_USR-xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
MERCADOPAGO_ACCESS_TOKEN=APP_USR-1234567890123456-123456-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-123456789
```

3. Cambia `VITE_APP_ENV=production` en frontend `.env`

### 6.2 Certificado SSL Obligatorio

Mercado Pago requiere HTTPS para webhooks en producción:

```bash
# Con Let's Encrypt (certbot)
sudo certbot --nginx -d tudominio.com
```

### 6.3 Configurar Webhook de Producción

URL: `https://tudominio.com/api/mercadopago/webhook`

### 6.4 Activar Aplicación

En Mercado Pago:
1. Ve a tu aplicación
2. **"Solicitar activación en producción"**
3. Completa el formulario de homologación
4. Espera aprobación (1-3 días hábiles)

---

## 📞 Soporte y Recursos

**Documentación Oficial:**
- https://www.mercadopago.com.co/developers/es/docs
- SDK PHP: https://github.com/mercadopago/sdk-php

**Panel de Mercado Pago:**
- https://www.mercadopago.com.co/developers

**Tarjetas de Prueba:**
- https://www.mercadopago.com.co/developers/es/docs/testing/test-cards

**Centro de Ayuda:**
- https://www.mercadopago.com.co/ayuda

---

## ✅ Checklist de Implementación

- [ ] Credenciales de Mercado Pago obtenidas
- [ ] SDK instalado en backend (`composer require mercadopago/dx-php`)
- [ ] Migración `payment_transactions` ejecutada
- [ ] Modelo `PaymentTransaction` creado
- [ ] Controlador `PaymentController` implementado
- [ ] Rutas de API agregadas
- [ ] Frontend actualizado con llamada a `/create-payment-preference`
- [ ] Vistas de éxito/error creadas
- [ ] Rutas de retorno configuradas
- [ ] Webhook configurado en Mercado Pago
- [ ] Testing con tarjetas de prueba completado
- [ ] Logs verificados (preferencia, webhook, activación)
- [ ] Credenciales de producción configuradas
- [ ] SSL/HTTPS habilitado
- [ ] Webhook de producción configurado
- [ ] Aplicación activada en Mercado Pago

---

## 🎯 Próximos Pasos Recomendados

1. **Renovaciones Automáticas:**
   - Implementar cron job para renovar suscripciones
   - Enviar emails de recordatorio antes de expiración

2. **Panel de Suscripciones:**
   - Vista para que usuarios vean su plan actual
   - Botón para cambiar de plan
   - Historial de pagos

3. **Facturación:**
   - Generar facturas PDF automáticamente
   - Enviar por email después de cada pago

4. **Métricas:**
   - Dashboard de ingresos (MRR, ARR)
   - Tasa de conversión por plan
   - Churn rate

---

**¡Listo! 🚀** Ahora tienes todo lo necesario para integrar Mercado Pago en tu sistema SaaS.
