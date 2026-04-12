<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\ConversationHistory;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Customer;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\ProductReturn;
use Carbon\Carbon;

class GeminiAgentService
{
    protected $apiKey;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';
    protected $sessionId;
    protected $maxHistoryMessages = 30; // 30 mensajes para mantener buen contexto de conversación

    public function __construct()
    {
        // Usar config() en lugar de env() para compatibilidad con config cacheado
        $this->apiKey = config('services.gemini.api_key');
    }

    /**
     * Obtiene o crea el session_id para el usuario actual
     * IMPORTANTE: El session_id es por usuario+tenant, NO por día
     * Esto permite mantener contexto entre conversaciones
     */
    private function getSessionId()
    {
        $userId = auth()->id() ?? 'guest_' . request()->ip();
        $tenantId = tenant('id') ?? 'default';
        // Session por usuario+tenant (sin fecha para mantener contexto)
        return 'gemini_sess_' . md5($userId . '_' . $tenantId);
    }

    /**
     * Construye el System Instruction para Gemini
     */
    private function buildSystemInstruction()
    {
        $currentDate = now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY');
        $currentTime = now()->format('H:i');

        // Obtener plan actual del tenant
        $tenantPlan = 'free_trial';
        $businessName = 'tu negocio';
        $storeType = 'general';
        try {
            if (function_exists('tenant')) {
                $tenant = tenant();
                $tenantPlan = $tenant ? ($tenant->plan ?? 'free_trial') : 'free_trial';
                $businessName = $tenant ? ($tenant->business_name ?? 'tu negocio') : 'tu negocio';
            }
            // Obtener tipo de tienda de la configuración
            $settings = \App\Models\SystemSetting::getSettings();
            $storeType = $settings->store_type ?? 'general';
        } catch (\Exception $e) {
            Log::warning('No se pudo obtener el plan del tenant: ' . $e->getMessage());
        }

        $planInfo = match($tenantPlan) {
            'free_trial' => 'TRIAL (Prueba Gratuita) - Funciones básicas limitadas.',
            'basic' => 'BÁSICO - Facturación e inventario simple. Sin multisede ni créditos.',
            'premium' => 'PREMIUM - Multisede, Creditienda (deudas), Reportes avanzados.',
            'enterprise' => 'ENTERPRISE - Todo ilimitado + API + Soporte prioritario.',
            default => 'BÁSICO'
        };

        // Información contextual según tipo de tienda
        $storeTypeInfo = match($storeType) {
            'fashion' => 'TIENDA DE MODA 👗 - Productos con variantes (tallas, colores). POS Fashion con selector de variantes.',
            'food' => 'TIENDA DE ALIMENTOS 🍔 - Productos perecederos con fechas de vencimiento.',
            'electronics' => 'TIENDA DE ELECTRÓNICOS 📱 - Productos con números de serie y garantías.',
            default => 'TIENDA GENERAL 📦 - Productos simples sin variantes. Inventario estándar.'
        };

        // Sección específica de conocimiento según tipo de tienda
        $storeTypeKnowledge = $this->getStoreTypeKnowledge($storeType);

        return <<<EOT
Eres "105 IA", el asistente virtual INTELIGENTE del sistema POS 105 para "{$businessName}". 
No eres un simple chatbot que responde - eres un ASISTENTE DE NEGOCIO que PIENSA, ANALIZA y ACTÚA.

🎭 TU PERSONALIDAD:
- Eres AMABLE, CÁLIDO y PROFESIONAL. Como un compañero de trabajo que siempre está dispuesto a ayudar.
- NUNCA seas cortante, seco o grosero. Siempre responde con buena actitud.
- Usa un tono cercano y positivo. Di "¡Claro!", "¡Por supuesto!", "Con gusto te ayudo".
- NUNCA digas "No puedo", "No tengo esa capacidad", "Eso no es posible". En su lugar, busca alternativas o usa tus herramientas.
- Si no encuentras datos, di algo como "No encontré resultados para eso, pero puedo buscar de otra forma" o "Parece que no hay registros aún".
- Sé empático: "Entiendo lo que necesitas", "Buena pregunta", "Déjame revisar eso por ti".
- Cuando des malas noticias (sin ventas, etc.), sé constructivo: "No hubo ventas hoy, pero puedes promocionar tus productos más populares".

🏢 CONTEXTO:
- Negocio: {$businessName}
- Tipo de tienda: {$storeTypeInfo}
- Plan: {$tenantPlan} ({$planInfo})
- País: Colombia 🇨🇴
- Fecha: {$currentDate}
- Hora: {$currentTime}

{$storeTypeKnowledge}

═══════════════════════════════════════════════════════════════
         📊 DATOS DEL NEGOCIO EN TIEMPO REAL (PRIORIDAD MÁXIMA)
═══════════════════════════════════════════════════════════════

⚠️ IMPORTANTE: Si el usuario envía datos con [DATOS DEL NEGOCIO EN TIEMPO REAL], 
USA ESOS DATOS para responder preguntas como:
- "¿cuántas devoluciones hemos hecho?" → Lee las devoluciones del contexto
- "¿cuánto vendí hoy/este mes?" → Lee las ventas del contexto
- "¿cuántos productos hay?" → Lee el inventario del contexto
- "¿cómo van las ganancias?" → Lee las ganancias del contexto
- "¿cuánto he gastado?" → Lee los gastos del contexto
- "¿la caja está abierta?" → Lee el estado de caja del contexto

❌ NO digas "no tengo herramienta" o "no puedo consultar" si los datos YA ESTÁN en el contexto.
✅ Responde directamente con los números que ves en el contexto.

⚠️ REGLA CRÍTICA: USA SIEMPRE LAS HERRAMIENTAS CUANDO PREGUNTEN POR DATOS
- "¿cuántas devoluciones?" → USA consultarDevoluciones (OBLIGATORIO)
- "¿hubo ventas hoy?" → USA consultarVentas (OBLIGATORIO)  
- "¿devoluciones de hoy/esta semana/este mes?" → USA consultarDevoluciones con el periodo correcto
- NUNCA digas "no tengo información de devoluciones" - TIENES la herramienta consultarDevoluciones
- NUNCA digas "no puedo consultar ventas" - TIENES la herramienta consultarVentas

═══════════════════════════════════════════════════════════════
            🧠 TU MENTALIDAD: PIENSA → ANALIZA → ACTÚA
═══════════════════════════════════════════════════════════════

ERES PROACTIVO, NO REACTIVO. Cuando el usuario dice algo:

1. **PIENSA**: ¿Qué quiere realmente? ¿Qué información necesito?
2. **ANALIZA**: ¿Tengo una herramienta para esto? ¿Qué datos debo consultar?
3. **ACTÚA**: Ejecuta las herramientas necesarias y entrega la respuesta COMPLETA.

❌ MAL (reactivo, preguntón):
Usuario: "Dame mis productos"
Tú: "¿Qué término de búsqueda quieres usar?" ← HORRIBLE

✅ BIEN (proactivo, inteligente):
Usuario: "Dame mis productos"  
Tú: *Usa listarProductos* → "Tienes 45 productos activos. Aquí están los primeros 15..."

❌ MAL:
Usuario: "Cuál factura me dio más ganancia?"
Tú: "No puedo calcular eso" ← INACEPTABLE

✅ BIEN:
Usuario: "Cuál factura me dio más ganancia?"
Tú: *Usa obtenerFacturaMasRentable* → "La factura FACT-0234 del 15/01 te dejó $125.000 de margen (42%)..."

❌ MAL:
Usuario: "No me sale el módulo de Creditienda"
Tú: "Eso no lo puedo solucionar" ← INÚTIL

✅ BIEN:
Usuario: "No me sale el módulo de Creditienda"
Tú: *Usa consultarConfiguracion* → "Veo que Creditienda está desactivada en tu configuración. ¿Quieres que la active?"
Usuario: "Sí"
Tú: *Usa actualizarConfiguracion* → "¡Listo! Ya activé Creditienda. ¿Te ayudo a crear tu primer crédito o prefieres hacerlo manual?"

═══════════════════════════════════════════════════════════════
                    🛡️ REGLAS INVIOLABLES
═══════════════════════════════════════════════════════════════

⚠️ REGLA CRÍTICA: NO INVENTES ACCIONES ⚠️
- NUNCA digas "ya lo activé" o "ya está activo" SIN haber llamado a la herramienta primero.
- Si el usuario dice "activa X" → DEBES llamar a actualizarConfiguracion ANTES de responder.
- Si el usuario dice "dame mis productos" → DEBES llamar a listarProductos ANTES de responder.
- NUNCA inventes que algo "ya está activo" sin verificar con consultarConfiguracion.
- Si no llamaste a una herramienta, NO puedes afirmar que hiciste una acción.

⚠️ REGLA CRÍTICA: RADIO Y NAVEGACIÓN ⚠️
- Si el usuario menciona "radio", "música", "préndeme", "pon" → DEBES llamar a controlarRadio.
- Si el usuario dice "llévame a X", "ir a X", "abre X" → DEBES llamar a navegarModulo.
- NUNCA digas "pongo la radio" sin llamar a controlarRadio primero.
- NUNCA digas "te llevo a X" sin llamar a navegarModulo primero.

📌 REGLA 1: ACTÚA, NO PREGUNTES
- Si el usuario pide algo claro → HAZLO inmediatamente.
- "Mis productos" → Lista productos (no preguntes "¿cuáles?")
- "Ventas de hoy" → Muestra ventas (no preguntes "¿qué quieres ver?")
- "Sí", "ok", "hazlo", "dale" → EJECUTA la última acción propuesta.

📌 REGLA 1.1: DA TODA LA INFORMACIÓN DE UNA VEZ
- Cuando muestres datos (devoluciones, ventas, facturas, etc.) SIEMPRE incluye TODOS los detalles disponibles.
- NO des un resumen corto y esperes a que pidan más. Da la info COMPLETA desde el inicio.
- Ejemplo devoluciones: Muestra número, cliente, total, razón, fecha, factura original, método de reembolso, quién la hizo.
- Ejemplo ventas: Muestra número, cliente, total, método de pago, fecha.
- Si el usuario pregunta "dame más info" sobre algo que YA consultaste → USA EL HISTORIAL de la conversación, no pidas datos de nuevo.
- RECUERDA los datos que ya consultaste en mensajes anteriores. No olvides la conversación.

⚠️⚠️⚠️ REGLA CRÍTICA: FECHAS ESPECÍFICAS = USA HERRAMIENTAS ⚠️⚠️⚠️
- Si el usuario pregunta por UNA FECHA ESPECÍFICA (ayer, el 27, martes, hace 2 días, semana pasada):
  → DEBES llamar a consultarVentas con fecha="YYYY-MM-DD" o periodo="ayer"
  → NUNCA respondas con datos del contexto para fechas pasadas
  → El contexto de pantalla solo tiene datos de HOY, no de fechas anteriores
  
- Ejemplos:
  • "¿ventas del 27 de enero?" → consultarVentas(fecha="2026-01-27") ← OBLIGATORIO
  • "¿cómo estuvo ayer?" → consultarVentas(periodo="ayer") ← OBLIGATORIO  
  • "ventas del martes" → Calcula fecha y usa consultarVentas(fecha="YYYY-MM-DD")
  
- Si NO usas la herramienta para fechas pasadas, darás datos INCORRECTOS.

📌 REGLA 1.5: NO PREGUNTES POR PERÍODO
- "Producto más vendido" → Consulta TODO el histórico (no preguntes "¿de qué período?")
- "Cuántos proveedores tengo" → Consulta TODOS (no digas "no puedo")
- Si el usuario dice "en total" o "histórico" → USA TODO lo que hay en la base de datos.
- Solo pregunta por período si el usuario explícitamente dice "de este mes" o similar.
- Tienes herramientas para PROVEEDORES, PRODUCTOS MÁS VENDIDOS, etc. ¡ÚSALAS!

📌 REGLA 2: VERDAD ABSOLUTA (Anti-Alucinación)
- NUNCA inventes datos. Si no existen, di "No encontré X".
- SIEMPRE usa las herramientas para obtener datos REALES.
- Si buscas y hay 0 resultados → "No encontré productos con ese nombre"
- Si hay 5 resultados → "Encontré 5 productos" (número exacto, nunca "varios")

📌 REGLA 3: RESUELVE PROBLEMAS
- Si algo no está disponible → PIENSA por qué y ofrece solución.
- "No me aparece X" → Consulta configuración → Ofrece activar si está desactivado.
- "No puedo hacer Y" → Verifica si es limitación de plan → Explica y ofrece alternativa.

📌 REGLA 4: PROTEGE ACCIONES DESTRUCTIVAS
- ELIMINAR productos: Pide confirmación con nombre exacto.
- Eliminar TODOS los productos: PROHIBIDO. Di "No puedo eliminar todo, dime cuáles específicamente".
- ACTUALIZAR masivo: Muestra preview antes de aplicar.

📌 REGLA 4.5: CREACIÓN DE PRODUCTOS - RECOPILA TODOS LOS DATOS
Cuando el usuario quiera CREAR un producto, SIEMPRE debes recopilar estos datos OBLIGATORIOS antes de crear:
1. **Nombre** del producto (obligatorio)
2. **Precio de venta** (obligatorio)  
3. **Costo** del producto (obligatorio para calcular margen)
4. **Stock inicial** (obligatorio - ¿cuántas unidades tiene?)
5. **Categoría** (obligatorio)

⚠️ NUNCA crees un producto sin preguntar por el STOCK. Ejemplo de flujo correcto:
- Usuario: "Quiero crear un producto"
- Tú: "¡Perfecto! Vamos a crearlo. ¿Cómo se llama el producto?"
- Usuario: "Coca-Cola"
- Tú: "Genial, Coca-Cola. ¿Cuál es el precio de venta?"
- Usuario: "3000"
- Tú: "¿Y cuánto te costó? (precio de compra)"
- Usuario: "2000"
- Tú: "¿Cuántas unidades tienes en stock?" ← ⚠️ SIEMPRE PREGUNTA ESTO
- Usuario: "50"
- Tú: "¿En qué categoría lo pongo? (Bebidas, Snacks, etc.)"
- Usuario: "Bebidas"
- Tú: *Crea el producto con todos los datos*

📌 REGLA 5: CONTEXTO COLOMBIANO 🇨🇴
- Precios: $15.000 (punto para miles, sin decimales)
- Fechas: dd/mm/yyyy
- Saludo: Buenos días/tardes/noches según hora actual.

📌 REGLA 6: SÉ CONCISO PERO COMPLETO
- Respuestas directas sin rodeos.
- Si hay datos, muéstralos organizados.
- Usa emojis moderados (✅ ❌ 📦 💰 📊).
- Nunca digas "backend", "API", "query" - habla como humano.

📌 REGLA 7: SALUDOS Y CORTESÍA - SÉ AMABLE Y CÁLIDO
- Si el usuario te saluda ("hola", "hey", "buenas", etc.) → Responde con calidez y brevedad.
- Ejemplo: "¡Hola! 👋 ¿En qué te puedo ayudar hoy?"
- NUNCA respondas de forma seca como "¿Qué necesitas?" o "Dime".
- Sé profesional pero cercano, como un asistente amigable.
- Si no tienes datos del negocio en el mensaje, no inventes métricas ni digas "veo que vendiste X".
- Simplemente saluda y ofrece ayuda.

═══════════════════════════════════════════════════════════════
                    🔧 TUS HERRAMIENTAS
═══════════════════════════════════════════════════════════════

📦 PRODUCTOS:
• listarProductos - Listar TODOS los productos (sin necesidad de búsqueda)
• consultarInventario - Buscar productos por nombre/SKU  
• crearProducto - Crear nuevo producto
• actualizarProducto - Modificar precio/stock
• eliminarProducto - Eliminar producto (requiere confirmación)
• obtenerEstadisticas - Estadísticas generales del negocio
• productoMasVendido - El producto MÁS VENDIDO de TODO el histórico (NO pide período)
• productosPocoVendidos - Productos sin rotación CON RECOMENDACIONES ACCIONABLES

💰 VENTAS Y FACTURACIÓN:
• consultarVentas - Ver ventas por período
• consultarFacturas - Buscar facturas por cliente, número o período
• obtenerUltimaFactura - Obtiene la ÚLTIMA factura generada con TODOS sus detalles
• obtenerDetalleFactura - Ver detalle de una factura específica por ID
• obtenerReporteVentas - Resumen de ventas con métricas
• obtenerFacturaMasRentable - La factura con mayor margen de ganancia

🔄 DEVOLUCIONES:
• consultarDevoluciones - Ver devoluciones por período, fecha o estado. SIEMPRE úsala cuando pregunten por devoluciones.

👥 CLIENTES:
• consultarClientes - Buscar clientes
• obtenerMejoresClientes - Top clientes por ventas
• consultarDeudasClientes - Clientes con créditos pendientes

🏭 PROVEEDORES:
• consultarProveedores - Listar/buscar proveedores con estadísticas

⚙️ CONFIGURACIÓN DEL SISTEMA:
• consultarConfiguracion - Ver estado de módulos y opciones
• actualizarConfiguracion - Activar/desactivar módulos
• consultarSedes - Ver sedes registradas
• crearSede - Crear nueva sede

📂 IMPORTACIÓN:
• analizarArchivoProductos - Analizar Excel/CSV
• importarProductosMasivo - Importar productos en lote

🚀 NAVEGACIÓN Y CONTROL DEL SISTEMA:
• navegarModulo - Lleva al usuario a un módulo específico (productos, ventas, clientes, etc.)
• controlarRadio - Controla la radio del sistema (play, pause, siguiente, anterior)

═══════════════════════════════════════════════════════════════

🎯 NAVEGACIÓN INTELIGENTE:
Cuando el usuario diga:
- "llévame a productos" → Usa navegarModulo con modulo="productos"
- "quiero ver mis clientes" → Usa navegarModulo con modulo="clientes"
- "préndeme la radio" / "pon música" → Usa controlarRadio con accion="play"
- "para la radio" / "apaga la música" → Usa controlarRadio con accion="pause"
- "siguiente canción" → Usa controlarRadio con accion="next"

Después de crear un producto, puedes sugerir: "¿Quieres que te lleve al módulo de productos?"

🎯 TU MISIÓN: Que el dueño del negocio sienta que tiene un asistente BRILLANTE que ENTIENDE, PIENSA y RESUELVE. No un bot tonto que solo sigue reglas.

¡Ahora ve y sorprende al usuario con tu inteligencia!
EOT;
    }

    /**
     * Retorna conocimiento específico según el tipo de tienda
     * Para que la IA entienda las diferencias entre Fashion y General
     */
    private function getStoreTypeKnowledge(string $storeType): string
    {
        if ($storeType === 'fashion') {
            return <<<EOT
═══════════════════════════════════════════════════════════════
         👗 TIENDA DE MODA - CONOCIMIENTO ESPECIALIZADO
═══════════════════════════════════════════════════════════════

⚠️ ESTA ES UNA TIENDA DE MODA (FASHION). Esto significa:

📦 PRODUCTOS CON VARIANTES:
- Los productos tienen VARIANTES (tallas: XS, S, M, L, XL, XXL y colores: Azul, Negro, Blanco, etc.)
- El STOCK está distribuido entre las variantes, no en el producto principal
- Cuando el usuario pregunte "¿cuánto stock tengo de X?", suma todas las variantes
- Ejemplo: Camiseta Básica → Talla M Azul (5 unidades), Talla L Negro (3 unidades) = 8 unidades totales

📊 ESTRUCTURA DE DATOS FASHION:
- products → Producto padre (nombre, descripción, imagen principal)
- product_variants → Variantes con talla, color, SKU único, precio y stock individual
- product_options → Define las opciones disponibles (Talla, Color)
- product_option_values → Los valores posibles (S, M, L / Rojo, Azul)

💡 CONSULTAS INTELIGENTES PARA MODA:
- "¿Cuántas tallas XL tengo?" → Buscar en product_variants por talla
- "¿Qué colores hay del vestido X?" → Listar variantes únicas por color
- "Productos sin stock en talla M" → Variantes con stock=0 y talla=M
- "¿Qué tallas se venden más?" → Analizar ventas por variante

🛒 POS FASHION (Punto de Venta):
- El POS de moda muestra un selector de TALLA y COLOR antes de agregar al carrito
- No se puede vender un producto sin seleccionar la variante
- El precio puede variar entre variantes (ej: XXL más caro)
- El descuento puede aplicarse a nivel de variante

🎯 TÉRMINOS DE MODA QUE DEBES CONOCER:
- "Prenda" = Producto de ropa
- "Referencia" = El SKU o código del producto
- "Línea" = Categoría (ej: Línea casual, Línea formal)
- "Temporada" = Colección por época del año
- "Outlet" = Productos de temporadas pasadas con descuento

⚠️ ERRORES A EVITAR:
- NO mostrar stock del producto padre como total (ese está calculado)
- NO crear productos simples en tiendas fashion (deben tener variantes)
- SIEMPRE preguntar talla/color cuando el usuario quiere información específica
EOT;
        }

        // Para tienda general u otros tipos
        return <<<EOT
═══════════════════════════════════════════════════════════════
         📦 TIENDA GENERAL - CONOCIMIENTO ESPECIALIZADO  
═══════════════════════════════════════════════════════════════

📦 PRODUCTOS SIMPLES:
- Los productos NO tienen variantes (sin tallas ni colores)
- Cada producto tiene UN solo SKU, precio y stock
- El stock se maneja directamente en el producto
- Ideal para: ferreterías, papelerías, minimarkets, licoreras, etc.

📊 ESTRUCTURA DE DATOS GENERAL:
- products → Producto con nombre, SKU, precio, stock, costo
- product_warehouse → Stock por bodega (si tiene multisede)
- Sin tablas de variantes

💡 CONSULTAS TÍPICAS:
- "¿Cuánto tengo de X?" → Stock directo del producto
- "Productos agotados" → productos con stock <= 0
- "Productos por vencer" → Si maneja fechas de vencimiento

🛒 POS GENERAL (Punto de Venta):
- El POS general permite agregar productos directamente
- Sin selector de variantes
- Cantidad se ajusta en el carrito

🎯 TÉRMINOS COMUNES:
- "Ítem" = Producto
- "Referencia" = SKU o código
- "Línea" = Categoría de productos
- "Margen" = Diferencia entre precio venta y costo
EOT;
    }

    /**
     * Recupera el historial de conversación para Gemini
     */
    private function getConversationHistory()
    {
        $sessionId = $this->getSessionId();

        $messages = ConversationHistory::where('session_id', $sessionId)
            ->orderBy('created_at', 'desc')
            ->limit($this->maxHistoryMessages)
            ->get()
            ->reverse();

        $geminiHistory = [];
        foreach ($messages as $msg) {
            $role = $msg->role === 'user' ? 'user' : 'model';
            $geminiHistory[] = [
                'role' => $role,
                'parts' => [['text' => $msg->content]]
            ];
        }

        return $geminiHistory;
    }

    /**
     * Limpia SOLO mensajes antiguos (>48 horas) para mantener contexto reciente
     * CRÍTICO: Mantener 2 días de historial para contexto entre sesiones
     */
    private function cleanOldHistory()
    {
        try {
            // Solo eliminar mensajes de más de 48 horas (mantener 2 días de conversación)
            $deleted = ConversationHistory::where('created_at', '<', now()->subHours(48))->delete();

            if ($deleted > 0) {
            }

        } catch (\Exception $e) {
            Log::error('❌ [Gemini] Error limpiando historial: ' . $e->getMessage());
        }
    }

    /**
     * Limpia TODO el historial de una sesión específica (solo cuando el usuario lo solicita)
     */
    public function clearSessionHistory()
    {
        try {
            $sessionId = $this->getSessionId();
            $deleted = ConversationHistory::where('session_id', $sessionId)->delete();
            return $deleted;
        } catch (\Exception $e) {
            Log::error('❌ [Gemini] Error limpiando sesión: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Guarda un mensaje en el historial
     */
    private function saveMessage($role, $content)
    {
        ConversationHistory::create([
            'user_id' => auth()->id(),
            'session_id' => $this->getSessionId(),
            'role' => $role,
            'content' => $content
        ]);
    }

    /**
     * Ejecuta el agente Gemini con el prompt del usuario.
     * @param string $prompt - Mensaje del usuario
     * @param string|null $screenContext - Contexto de pantalla del frontend (datos del negocio)
     */
    public function runAgent($prompt, $screenContext = null)
    {
        $startTime = microtime(true);
        
        // Validar API Key
        if (!$this->apiKey) {
            Log::error('❌ [Gemini] API Key no configurada');
            return [
                'reply' => '⚠️ El asistente no está disponible en este momento. Por favor contacta al administrador.',
                'status' => 'error'
            ];
        }

        // Validar prompt vacío
        $prompt = trim($prompt);
        if (empty($prompt)) {
            return [
                'reply' => '¿En qué puedo ayudarte? Escribe tu pregunta o solicitud.',
                'status' => 'success'
            ];
        }
        
        // 🧠 OPTIMIZACIÓN: Guardar SOLO el mensaje del usuario en historial (SIN contexto)
        // Esto evita que el historial crezca exponencialmente con datos del negocio repetidos
        $originalUserMessage = $prompt;
        
        // 🧠 Si hay contexto de pantalla, agregarlo al prompt DESPUÉS de guardar el original
        if (!empty($screenContext)) {
            $prompt = "[DATOS DEL NEGOCIO EN TIEMPO REAL - USA ESTA INFORMACIÓN PARA RESPONDER]\n" . $screenContext . "\n\n[PREGUNTA DEL USUARIO]\n" . $prompt;
        }

        try {
            // Limpiar historial antiguo (>24h) - NO el historial actual
            $this->cleanOldHistory();

            // Guardar SOLO el mensaje original del usuario (SIN contexto de negocio)
            // Esto evita que el historial crezca de 7k a 12k tokens por mensaje
            $this->saveMessage('user', $originalUserMessage);

            // 1. Definir herramientas (Tools)
            $tools = $this->getToolsDefinition();

            // 2. Construir mensajes con historial
            $history = $this->getConversationHistory();

            // El historial ya incluye el mensaje actual, así que lo usamos directamente
            $messages = $history;

            // Si el historial está vacío (primera vez), agregamos el mensaje
            if (empty($messages)) {
                $messages = [
                    [
                        'role' => 'user',
                        'parts' => [['text' => $prompt]]
                    ]
                ];
            }

            $response = $this->callGemini($messages, $tools);

            // Validar estructura de respuesta
            if (!isset($response['candidates']) || !is_array($response['candidates']) || count($response['candidates']) === 0) {
                Log::warning('Gemini: No candidates returned', ['response' => $response]);
                
                // Verificar si fue bloqueado por seguridad
                if (isset($response['promptFeedback']['blockReason'])) {
                    return [
                        'reply' => 'No puedo responder a eso. Por favor reformula tu pregunta.',
                        'status' => 'blocked'
                    ];
                }
                
                return [
                    'reply' => 'Tuve un problema procesando tu solicitud. Por favor intenta de nuevo.',
                    'status' => 'error'
                ];
            }

            $candidate = $response['candidates'][0];
            
            // Verificar finish reason
            $finishReason = $candidate['finishReason'] ?? 'STOP';
            if ($finishReason === 'SAFETY') {
                return [
                    'reply' => 'No puedo ayudarte con eso. ¿Hay algo más en lo que pueda asistirte?',
                    'status' => 'blocked'
                ];
            }
            
            $content = $candidate['content'] ?? null;
            $parts = $content['parts'] ?? [];

            if (empty($parts)) {
                Log::warning('Gemini: No content parts returned', ['candidate' => $candidate]);
                return [
                    'reply' => 'No obtuve una respuesta clara. ¿Puedes reformular tu pregunta?',
                    'status' => 'error'
                ];
            }

            $firstPart = $parts[0];

            // 3. Verificar si Gemini quiere ejecutar una función
            if (isset($firstPart['functionCall'])) {
                $result = $this->handleFunctionCalls($parts, $messages, $tools);
                $this->logAIUsage($prompt, $result, $startTime);
                return $result;
            }

            // Si no hubo llamada a función, devolver la respuesta de texto normal
            $replyText = $firstPart['text'] ?? 'No entendí tu solicitud. ¿Puedes ser más específico?';

            // 🛡️ FILTRO: Detectar si Gemini respondió con código técnico en lugar de ejecutar función
            // Esto pasa a veces cuando el modelo "alucina" y genera código Python/JS
            $replyText = $this->sanitizeAIResponse($replyText, $prompt);

            // Guardar respuesta en historial
            $this->saveMessage('assistant', $replyText);

            // Extraer información de tokens
            $tokensInfo = $this->extractTokensInfo($response);

            $result = [
                'reply' => $replyText,
                'status' => 'success',
                'tokens' => $tokensInfo
            ];

            $this->logAIUsage($prompt, $result, $startTime);

            return $result;

        } catch (\Exception $e) {
            Log::error('❌ [Gemini] Error en runAgent: ' . $e->getMessage(), [
                'prompt' => substr($prompt, 0, 100),
                'trace' => $e->getTraceAsString()
            ]);

            // Guardar mensaje de error en historial para mantener contexto
            $this->saveMessage('assistant', 'Tuve un problema técnico al procesar tu solicitud.');

            return [
                'reply' => 'Tuve un problema técnico. Por favor intenta de nuevo en unos segundos.',
                'status' => 'error'
            ];
        }
    }

    /**
     * Ejecuta importación de productos directamente desde el controlador
     * Evita pasar por el prompt de Gemini para archivos grandes
     */
    public function executeImportFromController($args)
    {

        return $this->importarProductosMasivoHandler($args);
    }

    /**
     * Extrae información de tokens de la respuesta de Gemini
     */
    private function extractTokensInfo($response)
    {
        if (!isset($response['usageMetadata'])) {
            return null;
        }
        
        $usage = $response['usageMetadata'];
        return [
            'promptTokens' => $usage['promptTokenCount'] ?? 0,
            'candidatesTokens' => $usage['candidatesTokenCount'] ?? 0,
            'totalTokens' => $usage['totalTokenCount'] ?? 0,
        ];
    }

    /**
     * Registra el uso de la IA para auditoría
     */
    private function logAIUsage($prompt, $result, $startTime)
    {
        try {
            $responseTime = round((microtime(true) - $startTime) * 1000); // en ms
            
            $promptTokens = $result['tokens']['promptTokens'] ?? 0;
            $completionTokens = $result['tokens']['candidatesTokens'] ?? 0;
            $costUsd = \App\Models\AiUsageLog::calculateTextCost($promptTokens, $completionTokens, 'gemini');
            
            \App\Models\AiUsageLog::create([
                'user_id' => auth()->id(),
                'api_key_index' => 0,
                'api_key_last_4' => substr($this->apiKey, -4),
                'user_message' => substr($prompt, 0, 500),
                'prompt_tokens' => $promptTokens,
                'completion_tokens' => $completionTokens,
                'total_tokens' => $result['tokens']['totalTokens'] ?? 0,
                'status' => $result['status'],
                'error_message' => $result['status'] === 'error' ? substr($result['reply'], 0, 255) : null,
                'response_time_ms' => $responseTime,
                'model' => 'gemini-2.0-flash',
                'provider' => 'gemini',
                'endpoint' => 'generateContent',
                'request_type' => 'chat',
                'cost_usd' => $costUsd,
                'ip_address' => request()->ip()
            ]);
        } catch (\Exception $e) {
            // No fallar si el logging falla
            Log::warning('No se pudo registrar uso de IA: ' . $e->getMessage());
        }
    }

    /**
     * Maneja las llamadas a funciones de Gemini
     */
    private function handleFunctionCalls($parts, $messages, $tools)
    {
        $functionResults = [];
        $executedFunctions = [];
        $actions = []; // Para almacenar acciones de navegación/radio

        foreach ($parts as $part) {
            if (!isset($part['functionCall'])) continue;

            $functionCall = $part['functionCall'];
            $functionName = $functionCall['name'];
            $args = $functionCall['args'] ?? [];

            // Convertir args vacío a objeto
            if (is_array($args) && empty($args)) {
                $args = new \stdClass();
            }


            // Ejecutar la función
            $functionResult = $this->executeFunction($functionName, (array)$args);
            $functionResults[] = [
                'name' => $functionName,
                'result' => $functionResult
            ];
            $executedFunctions[] = $functionName;

            // Capturar acciones de navegación/radio
            if (isset($functionResult['action'])) {
                $actions[] = $functionResult['action'];
            }

            // Agregar respuesta de función al historial
            $messages[] = [
                'role' => 'model',
                'parts' => [['functionCall' => [
                    'name' => $functionName,
                    'args' => $args
                ]]]
            ];

            $messages[] = [
                'role' => 'function',
                'parts' => [[
                    'functionResponse' => [
                        'name' => $functionName,
                        'response' => ['content' => $functionResult]
                    ]
                ]]
            ];
        }

        // Segunda llamada para obtener respuesta final
        $finalResponse = $this->callGemini($messages, $tools);

        if (isset($finalResponse['candidates'][0]['content']['parts'][0]['text'])) {
            $replyText = $finalResponse['candidates'][0]['content']['parts'][0]['text'];
        } else {
            // Fallback inteligente
            $resultMessages = array_map(function($r) {
                return $r['result']['message'] ?? $r['result']['mensaje'] ?? json_encode($r['result']);
            }, $functionResults);
            $replyText = implode("\n", $resultMessages);
        }

        // 🛡️ Sanitizar respuesta final también (por si acaso)
        $replyText = $this->sanitizeAIResponse($replyText, $messages[0]['parts'][0]['text'] ?? '');

        // Guardar respuesta en historial
        $this->saveMessage('assistant', $replyText);

        // Extraer información de tokens
        $tokensInfo = null;
        if (isset($finalResponse['usageMetadata'])) {
            $usage = $finalResponse['usageMetadata'];
            $tokensInfo = [
                'promptTokens' => $usage['promptTokenCount'] ?? 0,
                'candidatesTokens' => $usage['candidatesTokenCount'] ?? 0,
                'totalTokens' => $usage['totalTokenCount'] ?? 0,
            ];
        }

        $result = [
            'reply' => $replyText,
            'status' => 'success',
            'agent_action' => 'Ejecutado: ' . implode(', ', $executedFunctions),
            'tokens' => $tokensInfo
        ];

        // Agregar la primera acción si existe (navegación/radio)
        if (!empty($actions)) {
            $result['action'] = $actions[0];
        }

        return $result;
    }

    /**
     * Define todas las herramientas disponibles para Gemini
     */
    private function getToolsDefinition()
    {
        return [
            [
                'function_declarations' => [
                    // === PRODUCTOS ===
                    [
                        'name' => 'crearProducto',
                        'description' => 'Crea un nuevo producto. IMPORTANTE: Antes de usar esta función, DEBES haber preguntado al usuario por: nombre, precio de venta, costo, stock inicial y categoría. NO crees productos sin stock.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'nombre' => ['type' => 'STRING', 'description' => 'Nombre del producto (obligatorio)'],
                                'precio' => ['type' => 'NUMBER', 'description' => 'Precio de venta del producto (obligatorio)'],
                                'costo' => ['type' => 'NUMBER', 'description' => 'Precio de costo/compra del producto'],
                                'stock' => ['type' => 'NUMBER', 'description' => 'Cantidad inicial en stock (obligatorio - siempre pregunta cuántas unidades tiene)'],
                                'categoria' => ['type' => 'STRING', 'description' => 'Nombre de la categoría del producto']
                            ],
                            'required' => ['nombre', 'precio', 'stock']
                        ]
                    ],
                    [
                        'name' => 'consultarInventario',
                        'description' => 'Busca productos en el inventario por nombre o término de búsqueda.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'query' => ['type' => 'STRING', 'description' => 'Término de búsqueda']
                            ],
                            'required' => ['query']
                        ]
                    ],
                    [
                        'name' => 'obtenerEstadisticas',
                        'description' => 'Obtiene el conteo total de productos, valor del inventario, productos con stock bajo y ventas de hoy.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => new \stdClass()
                        ]
                    ],
                    [
                        'name' => 'actualizarProducto',
                        'description' => 'Actualiza el precio o stock de un producto existente buscándolo por nombre.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'nombre' => ['type' => 'STRING', 'description' => 'Nombre exacto del producto a actualizar'],
                                'nuevo_precio' => ['type' => 'NUMBER', 'description' => 'Nuevo precio (opcional)'],
                                'nuevo_stock' => ['type' => 'NUMBER', 'description' => 'Nuevo stock (opcional)']
                            ],
                            'required' => ['nombre']
                        ]
                    ],
                    [
                        'name' => 'eliminarProducto',
                        'description' => 'Elimina un producto del inventario por su nombre.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'nombre' => ['type' => 'STRING', 'description' => 'Nombre exacto del producto a eliminar']
                            ],
                            'required' => ['nombre']
                        ]
                    ],

                    // === VENTAS ===
                    [
                        'name' => 'consultarVentas',
                        'description' => 'Consulta las ventas realizadas. Puede buscar por período (hoy, ayer, semana, mes) o por fecha específica (YYYY-MM-DD). Usa esto cuando pregunten si hubo ventas, cuántas ventas, total de ventas, etc.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'periodo' => [
                                    'type' => 'STRING',
                                    'description' => 'Período de tiempo: hoy, ayer, semana, mes, año, ultimos_7_dias, ultimos_30_dias',
                                    'enum' => ['hoy', 'ayer', 'semana', 'mes', 'año', 'ultimos_7_dias', 'ultimos_30_dias']
                                ],
                                'fecha' => [
                                    'type' => 'STRING',
                                    'description' => 'Fecha específica en formato YYYY-MM-DD (ej: 2025-12-27). Usa esto si el usuario pregunta por una fecha específica.'
                                ],
                                'limite' => [
                                    'type' => 'NUMBER',
                                    'description' => 'Número máximo de facturas a mostrar (por defecto 10)'
                                ]
                            ]
                        ]
                    ],
                    [
                        'name' => 'obtenerReporteVentas',
                        'description' => 'Obtiene un resumen completo de ventas con métricas: total vendido, cantidad de facturas, ticket promedio, productos más vendidos.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'periodo' => [
                                    'type' => 'STRING',
                                    'description' => 'Período: hoy, ayer, semana, mes',
                                    'enum' => ['hoy', 'ayer', 'semana', 'mes']
                                ]
                            ],
                            'required' => ['periodo']
                        ]
                    ],
                    [
                        'name' => 'obtenerProductosMenosVendidos',
                        'description' => 'Obtiene los productos con menor cantidad de ventas/rotación en un período. Útil para identificar productos estancados o sin movimiento.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'periodo' => [
                                    'type' => 'STRING',
                                    'description' => 'Período: semana, mes, trimestre, año',
                                    'enum' => ['semana', 'mes', 'trimestre', 'año']
                                ],
                                'limite' => ['type' => 'NUMBER', 'description' => 'Cantidad de productos a mostrar (default 10)']
                            ]
                        ]
                    ],
                    [
                        'name' => 'obtenerMejoresClientes',
                        'description' => 'Obtiene los clientes que más han comprado (mayor monto total) en un período.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'periodo' => [
                                    'type' => 'STRING',
                                    'description' => 'Período: hoy, semana, mes, año',
                                    'enum' => ['hoy', 'semana', 'mes', 'año']
                                ],
                                'limite' => ['type' => 'NUMBER', 'description' => 'Cantidad de clientes a mostrar (default 10)']
                            ]
                        ]
                    ],

                    // === CLIENTES ===
                    [
                        'name' => 'consultarClientes',
                        'description' => 'Busca clientes por nombre, teléfono, email o documento.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'query' => ['type' => 'STRING', 'description' => 'Término de búsqueda (nombre, teléfono, email)'],
                                'limite' => ['type' => 'NUMBER', 'description' => 'Máximo de resultados (default 10)']
                            ]
                        ]
                    ],

                    // === FACTURAS ===
                    [
                        'name' => 'consultarFacturas',
                        'description' => 'Busca facturas por cliente, número de factura o período de tiempo. Para obtener la última factura, usa orden="reciente" y limite=1.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'cliente' => ['type' => 'STRING', 'description' => 'Nombre del cliente'],
                                'numero_factura' => ['type' => 'STRING', 'description' => 'Número de factura (ej: FACT-0001)'],
                                'periodo' => [
                                    'type' => 'STRING',
                                    'enum' => ['hoy', 'ayer', 'semana', 'mes', 'todo']
                                ],
                                'orden' => [
                                    'type' => 'STRING',
                                    'enum' => ['reciente', 'antiguo', 'mayor_monto', 'menor_monto'],
                                    'description' => 'Orden de resultados: reciente (más nueva primero), antiguo, mayor_monto, menor_monto'
                                ],
                                'limite' => ['type' => 'NUMBER', 'description' => 'Máximo de resultados (default 10, para última factura usa 1)']
                            ]
                        ]
                    ],
                    [
                        'name' => 'obtenerUltimaFactura',
                        'description' => 'Obtiene la última factura generada en el sistema con todos sus detalles: número, cliente, productos, total, fecha.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => new \stdClass()
                        ]
                    ],
                    [
                        'name' => 'obtenerDetalleFactura',
                        'description' => 'Obtiene los detalles completos de una factura: productos, cantidades, precios, cliente, etc.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'factura_id' => ['type' => 'NUMBER', 'description' => 'ID de la factura']
                            ],
                            'required' => ['factura_id']
                        ]
                    ],

                    // === CATEGORÍAS ===
                    [
                        'name' => 'consultarCategorias',
                        'description' => 'Lista todas las categorías de productos o busca una específica.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'query' => ['type' => 'STRING', 'description' => 'Término de búsqueda (opcional)']
                            ]
                        ]
                    ],

                    // === PREMIUM: CRÉDITOS Y SEDES ===
                    [
                        'name' => 'consultarDeudasClientes',
                        'description' => 'Busca clientes que tienen deudas pendientes (créditos activos). Útil para saber quién debe dinero.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'minimo_deuda' => ['type' => 'NUMBER', 'description' => 'Monto mínimo de deuda a buscar (opcional, default 0)'],
                                'limite' => ['type' => 'NUMBER', 'description' => 'Máximo de resultados (default 10)']
                            ]
                        ]
                    ],
                    [
                        'name' => 'consultarSedes',
                        'description' => 'Lista las sedes (bodegas/sucursales) registradas en el sistema.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => new \stdClass()
                        ]
                    ],
                    [
                        'name' => 'crearSede',
                        'description' => 'Crea una nueva sede (bodega/sucursal). Verifica automáticamente si el plan del usuario permite crear más sedes (Premium: max 3, Enterprise: ilimitado).',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'nombre' => ['type' => 'STRING', 'description' => 'Nombre de la nueva sede'],
                                'direccion' => ['type' => 'STRING', 'description' => 'Dirección de la sede (opcional)'],
                                'telefono' => ['type' => 'STRING', 'description' => 'Teléfono de la sede (opcional)']
                            ],
                            'required' => ['nombre']
                        ]
                    ],

                    // === IMPORTACIÓN DE PRODUCTOS ===
                    [
                        'name' => 'analizarArchivoProductos',
                        'description' => 'Analiza un archivo Excel o CSV que el usuario ha subido al chat. Usa esta herramienta cuando el usuario mencione que quiere importar productos desde Excel/CSV o cuando adjunte un archivo. La IA procesa automáticamente el archivo adjunto, detecta las columnas (nombre, precio, stock, etc.) y muestra un preview de los productos. NUNCA pidas al usuario "la ruta del archivo" - el sistema detecta automáticamente los archivos adjuntos.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'file_path' => [
                                    'type' => 'STRING',
                                    'description' => 'Ruta del archivo que el usuario subió. El sistema la detecta automáticamente desde los archivos adjuntos del mensaje. Busca en el contexto del mensaje si hay archivos adjuntos y usa su ruta.'
                                ]
                            ],
                            'required' => ['file_path']
                        ]
                    ],
                    [
                        'name' => 'importarProductosMasivo',
                        'description' => 'Importa múltiples productos a la base de datos después de que el usuario confirme la vista previa. Usa esto cuando ya hayas analizado el archivo con analizarArchivoProductos y el usuario confirme que quiere importar los productos. Crea automáticamente las categorías que no existan.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'products_data' => [
                                    'type' => 'OBJECT',
                                    'description' => 'Datos de productos obtenidos del análisis previo del archivo Excel/CSV',
                                    'properties' => [
                                        'headers' => [
                                            'type' => 'ARRAY',
                                            'description' => 'Array con los nombres de las columnas detectadas (ej: ["Nombre", "Precio Venta", "Stock", "Categoría"])',
                                            'items' => ['type' => 'STRING']
                                        ],
                                        'rows' => [
                                            'type' => 'ARRAY',
                                            'description' => 'Array de arrays con los datos de cada producto del archivo',
                                            'items' => [
                                                'type' => 'ARRAY',
                                                'items' => ['type' => 'STRING']
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            'required' => ['products_data']
                        ]
                    ],

                    // === NUEVAS HERRAMIENTAS INTELIGENTES ===
                    [
                        'name' => 'listarProductos',
                        'description' => 'Lista TODOS los productos del inventario sin necesidad de búsqueda. Usa esta herramienta cuando el usuario diga "mis productos", "dame los productos", "ver inventario completo", "lista de productos". NO PIDAS término de búsqueda, simplemente lista.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'limite' => ['type' => 'NUMBER', 'description' => 'Máximo de productos a mostrar (default 20)'],
                                'ordenar_por' => [
                                    'type' => 'STRING',
                                    'description' => 'Campo para ordenar: nombre, precio, stock, ventas',
                                    'enum' => ['nombre', 'precio', 'stock', 'ventas']
                                ],
                                'solo_activos' => ['type' => 'BOOLEAN', 'description' => 'Solo mostrar productos activos (default true)']
                            ]
                        ]
                    ],
                    [
                        'name' => 'consultarConfiguracion',
                        'description' => 'Consulta el estado de la configuración del sistema: módulos activos (Creditienda, descuentos, fidelización), opciones habilitadas, y configuraciones de la tienda. Usa esto cuando el usuario pregunte "está activo X", "por qué no me aparece X", "qué módulos tengo".',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'modulo' => [
                                    'type' => 'STRING',
                                    'description' => 'Módulo específico a consultar (opcional). Si no se especifica, devuelve todo.',
                                    'enum' => ['creditienda', 'descuentos', 'fidelizacion', 'iva', 'general']
                                ]
                            ]
                        ]
                    ],
                    [
                        'name' => 'actualizarConfiguracion',
                        'description' => 'OBLIGATORIO para activar o desactivar módulos del sistema. SIEMPRE usa esta función cuando el usuario diga: "activa X", "habilita X", "quiero usar X", "desactiva X", "actívalo", "sí actívalo". Los módulos son: creditienda, descuentos, fidelizacion (puntos/lealtad), iva. Esta función modifica la base de datos directamente. NO respondas que vas a activar algo sin llamar a esta función primero.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'modulo' => [
                                    'type' => 'STRING',
                                    'description' => 'Módulo a modificar: creditienda, descuentos, fidelizacion (sistema de puntos/lealtad), iva',
                                    'enum' => ['creditienda', 'descuentos', 'fidelizacion', 'iva']
                                ],
                                'activar' => ['type' => 'BOOLEAN', 'description' => 'true para activar, false para desactivar']
                            ],
                            'required' => ['modulo', 'activar']
                        ]
                    ],
                    [
                        'name' => 'obtenerFacturaMasRentable',
                        'description' => 'Obtiene la factura con mayor margen de ganancia (diferencia entre precio de venta y costo). Responde preguntas como "cuál factura me dio más ganancia", "venta más rentable", "mejor margen".',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'periodo' => [
                                    'type' => 'STRING',
                                    'description' => 'Período a analizar',
                                    'enum' => ['hoy', 'ayer', 'semana', 'mes', 'año']
                                ],
                                'limite' => ['type' => 'NUMBER', 'description' => 'Cantidad de facturas top a mostrar (default 5)']
                            ]
                        ]
                    ],
                    [
                        'name' => 'analizarNegocio',
                        'description' => 'Analiza el estado general del negocio y da recomendaciones inteligentes. Usa esto cuando el usuario pida "recomendaciones", "cómo mejorar ventas", "analiza mi negocio", "qué puedo hacer mejor".',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => new \stdClass()
                        ]
                    ],

                    // === NUEVAS: PROVEEDORES Y PRODUCTOS MÁS/MENOS VENDIDOS ===
                    [
                        'name' => 'consultarProveedores',
                        'description' => 'Lista TODOS los proveedores registrados con estadísticas: cuántos productos les compramos, deuda pendiente, último pedido. Usa cuando pregunten "cuántos proveedores tengo", "mis proveedores", "a quién le compro".',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'query' => ['type' => 'STRING', 'description' => 'Buscar proveedor por nombre (opcional)'],
                                'solo_activos' => ['type' => 'BOOLEAN', 'description' => 'Solo mostrar proveedores activos (default true)'],
                                'limite' => ['type' => 'NUMBER', 'description' => 'Máximo de proveedores a mostrar (default 20)']
                            ]
                        ]
                    ],
                    [
                        'name' => 'productoMasVendido',
                        'description' => 'Obtiene el producto MÁS VENDIDO de TODO el histórico. NO pidas período - usa TODA la base de datos. Responde "cuál es el producto que más he vendido", "producto más vendido", "qué se vende más". Si el usuario quiere un período específico, lo dirá explícitamente.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'limite' => ['type' => 'NUMBER', 'description' => 'Cantidad de productos top a mostrar (default 10)'],
                                'periodo' => [
                                    'type' => 'STRING',
                                    'description' => 'OPCIONAL - Solo usar si el usuario lo especifica explícitamente',
                                    'enum' => ['hoy', 'semana', 'mes', 'año', 'todo']
                                ]
                            ]
                        ]
                    ],
                    [
                        'name' => 'productosPocoVendidos',
                        'description' => 'Obtiene productos con poca o ninguna venta e incluye RECOMENDACIONES ACCIONABLES: bajar precio, crear promoción, enviar por WhatsApp a clientes. Usa cuando digan "productos que no se venden", "qué no he vendido", "productos estancados", "cómo mover inventario".',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'limite' => ['type' => 'NUMBER', 'description' => 'Cantidad de productos a mostrar (default 10)'],
                                'dias_sin_venta' => ['type' => 'NUMBER', 'description' => 'Días sin ventas para considerar estancado (default 30)']
                            ]
                        ]
                    ],

                    // === DEVOLUCIONES ===
                    [
                        'name' => 'consultarDevoluciones',
                        'description' => 'Consulta las devoluciones realizadas. Puede buscar por período (hoy, ayer, semana, mes) o por fecha específica (YYYY-MM-DD). Usa esto cuando pregunten por devoluciones, notas crédito, reembolsos, productos devueltos, etc.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'periodo' => [
                                    'type' => 'STRING',
                                    'description' => 'Período de tiempo: hoy, ayer, semana, mes, año, ultimos_7_dias, ultimos_30_dias',
                                    'enum' => ['hoy', 'ayer', 'semana', 'mes', 'año', 'ultimos_7_dias', 'ultimos_30_dias']
                                ],
                                'fecha' => [
                                    'type' => 'STRING',
                                    'description' => 'Fecha específica en formato YYYY-MM-DD'
                                ],
                                'estado' => [
                                    'type' => 'STRING',
                                    'description' => 'Estado de la devolución: completed, pending, cancelled',
                                    'enum' => ['completed', 'pending', 'cancelled']
                                ],
                                'limite' => [
                                    'type' => 'NUMBER',
                                    'description' => 'Número máximo de devoluciones a mostrar (por defecto 10)'
                                ]
                            ]
                        ]
                    ],

                    // === NAVEGACIÓN Y CONTROL DEL SISTEMA ===
                    [
                        'name' => 'navegarModulo',
                        'description' => 'Lleva al usuario a un módulo específico del sistema. Usa cuando digan "llévame a X", "quiero ver X", "abre X", "ir a X". Después de crear un producto, ofrece llevar al módulo de productos.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'modulo' => [
                                    'type' => 'STRING',
                                    'description' => 'Módulo destino. Opciones: dashboard, pos/ventas/vender, facturas/facturacion, devoluciones, productos/inventario, categorias, stock, clientes, proveedores/compras, usuarios, caja/cajas, gastos/egresos, reportes, configuracion/ajustes',
                                    'enum' => ['dashboard', 'pos', 'ventas', 'facturas', 'facturacion', 'devoluciones', 'productos', 'inventario', 'categorias', 'stock', 'clientes', 'proveedores', 'compras', 'usuarios', 'caja', 'cajas', 'gastos', 'gastos operativos', 'egresos', 'reportes', 'configuracion', 'ajustes']
                                ],
                                'mensaje' => ['type' => 'STRING', 'description' => 'Mensaje a mostrar al usuario antes de navegar']
                            ],
                            'required' => ['modulo']
                        ]
                    ],
                    [
                        'name' => 'controlarRadio',
                        'description' => 'OBLIGATORIO para controlar la radio. SIEMPRE usa esta función cuando el usuario mencione: "radio", "música", "pon música", "préndeme", "apaga", "siguiente canción", "anterior", "volumen". Esta función envía comandos REALES al reproductor de radio del sistema. NO respondas sobre la radio sin llamar a esta función.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'accion' => [
                                    'type' => 'STRING',
                                    'description' => 'play=reproducir/prender, pause=pausar/parar/apagar, next=siguiente, previous=anterior, volume_up=subir volumen, volume_down=bajar volumen, mute=silenciar',
                                    'enum' => ['play', 'pause', 'toggle', 'next', 'previous', 'volume_up', 'volume_down', 'mute']
                                ],
                                'volumen' => ['type' => 'NUMBER', 'description' => 'Nivel de volumen (0-100) si la acción es ajustar volumen']
                            ],
                            'required' => ['accion']
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Realiza la petición HTTP a la API de Gemini con reintentos y manejo de errores robusto.
     */
    private function callGemini($contents, $tools = null, $retryCount = 0)
    {
        $url = $this->baseUrl . '?key=' . $this->apiKey;
        $maxRetries = 2;

        $payload = [
            'contents' => $contents,
            'systemInstruction' => [
                'parts' => [['text' => $this->buildSystemInstruction()]]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'topP' => 0.95,
                'maxOutputTokens' => 2048
            ],
            'safetySettings' => [
                ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_ONLY_HIGH'],
                ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_ONLY_HIGH'],
                ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_ONLY_HIGH'],
                ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_ONLY_HIGH'],
            ]
        ];

        if ($tools) {
            $payload['tools'] = $tools;
            $payload['toolConfig'] = [
                'functionCallingConfig' => [
                    'mode' => 'AUTO'
                ]
            ];
        }

        // Log reducido para producción

        try {
            $response = Http::timeout(60)
                ->retry(2, 1000)
                ->withHeaders([
                    'Content-Type' => 'application/json'
                ])
                ->post($url, $payload);

            if ($response->failed()) {
                $statusCode = $response->status();
                $errorBody = $response->body();
                
                Log::error('❌ [Gemini API Error]', [
                    'status' => $statusCode, 
                    'body' => substr($errorBody, 0, 500)
                ]);
                
                if ($statusCode === 429) {
                    throw new \Exception('El asistente está ocupado. Por favor espera unos segundos.');
                }
                if ($statusCode === 503 || $statusCode === 500) {
                    if ($retryCount < $maxRetries) {
                        sleep(1);
                        return $this->callGemini($contents, $tools, $retryCount + 1);
                    }
                    throw new \Exception('El servicio no está disponible temporalmente.');
                }
                if ($statusCode === 400) {
                    throw new \Exception('No pude procesar tu solicitud. Intenta reformularla.');
                }
                
                throw new \Exception('Error de conexión con el asistente.');
            }

            $jsonResponse = $response->json();

            if (isset($jsonResponse['usageMetadata'])) {
                $usage = $jsonResponse['usageMetadata'];
            }

            if (!$jsonResponse) {
                throw new \Exception('Respuesta vacía del asistente.');
            }

            return $jsonResponse;

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('❌ [Gemini] Error de conexión: ' . $e->getMessage());
            throw new \Exception('No hay conexión con el servicio. Verifica tu internet.');
        }
    }

    /**
     * Ejecuta la función local correspondiente.
     */
    private function executeFunction($name, $args)
    {
        switch ($name) {
            // Productos
            case 'crearProducto':
                return $this->crearProductoDB($args);
            case 'consultarInventario':
                return $this->consultarInventarioDB($args);
            case 'obtenerEstadisticas':
                return $this->obtenerEstadisticasDB();
            case 'actualizarProducto':
                return $this->actualizarProductoDB($args);
            case 'eliminarProducto':
                return $this->eliminarProductoDB($args);

            // Ventas
            case 'consultarVentas':
                return $this->consultarVentasDB($args);
            case 'obtenerReporteVentas':
                return $this->obtenerReporteVentasDB($args);

            case 'obtenerProductosMenosVendidos':
                return $this->obtenerProductosMenosVendidosDB($args);

            case 'obtenerMejoresClientes':
                return $this->obtenerMejoresClientesDB($args);

            // Clientes
            case 'consultarClientes':
                return $this->consultarClientesDB($args);

            // Facturas
            case 'consultarFacturas':
                return $this->consultarFacturasDB($args);
            case 'obtenerDetalleFactura':
                return $this->obtenerDetalleFacturaDB($args);
            case 'obtenerUltimaFactura':
                return $this->obtenerUltimaFacturaDB();

            // Categorías
            case 'consultarCategorias':
                return $this->consultarCategoriasDB($args);

            // Premium
            case 'consultarDeudasClientes':
                return $this->consultarDeudasClientesDB($args);
            case 'consultarSedes':
                return $this->consultarSedesDB();
            case 'crearSede':
                return $this->crearSedeDB($args);

            // Importación de productos
            case 'analizarArchivoProductos':
                return $this->analizarArchivoProductosDB($args);
            case 'importarProductosMasivo':
                return $this->importarProductosMasivoHandler($args);

            // === NUEVAS HERRAMIENTAS INTELIGENTES ===
            case 'listarProductos':
                return $this->listarProductosDB($args);
            case 'consultarConfiguracion':
                return $this->consultarConfiguracionDB($args);
            case 'actualizarConfiguracion':
                return $this->actualizarConfiguracionDB($args);
            case 'obtenerFacturaMasRentable':
                return $this->obtenerFacturaMasRentableDB($args);
            case 'analizarNegocio':
                return $this->analizarNegocioDB();

            // === PROVEEDORES Y ANÁLISIS DE VENTAS ===
            case 'consultarProveedores':
                return $this->consultarProveedoresDB($args);
            case 'productoMasVendido':
                return $this->productoMasVendidoDB($args);
            case 'productosPocoVendidos':
                return $this->productosPocoVendidosDB($args);

            // === DEVOLUCIONES ===
            case 'consultarDevoluciones':
                return $this->consultarDevolucionesDB($args);

            // === NAVEGACIÓN Y CONTROL DEL SISTEMA ===
            case 'navegarModulo':
                return $this->navegarModuloAction($args);
            case 'controlarRadio':
                return $this->controlarRadioAction($args);

            default:
                return ['error' => 'Función no encontrada: ' . $name];
        }
    }

    // ========================================
    // FUNCIONES DE BASE DE DATOS - PRODUCTOS
    // ========================================

    private function crearProductoDB($data)
    {
        try {
            // 1. Validar datos mínimos
            $nombre = trim($data['nombre'] ?? '');
            $precio = floatval($data['precio'] ?? 0);
            $costo = floatval($data['costo'] ?? 0);
            $stock = intval($data['stock'] ?? 0);
            $categoriaNombre = trim($data['categoria'] ?? '');

            // Validación de nombre
            if (empty($nombre) || $nombre === 'Producto sin nombre') {
                return [
                    'status' => 'error',
                    'message' => "Necesito el nombre del producto para crearlo. ¿Cómo se llama?"
                ];
            }

            // Validación de precio
            if ($precio <= 0) {
                return [
                    'status' => 'error',
                    'message' => "Necesito el precio de venta del producto. ¿A cuánto lo vas a vender?"
                ];
            }

            // Validación de stock - SIEMPRE debe tener stock
            if ($stock <= 0) {
                return [
                    'status' => 'error',
                    'message' => "¿Cuántas unidades tienes en stock de {$nombre}? Necesito saber la cantidad inicial."
                ];
            }

            // Si no hay costo, estimar como 60% del precio
            if ($costo <= 0) {
                $costo = $precio * 0.6;
            }

            // Verificar si ya existe un producto con ese nombre
            $existente = Product::where('name', 'LIKE', $nombre)->where('active', true)->first();
            if ($existente) {
                return [
                    'status' => 'duplicate',
                    'existing_product' => [
                        'id' => $existente->id,
                        'name' => $existente->name,
                        'price' => $existente->sale_price,
                        'stock' => $existente->current_stock
                    ],
                    'message' => "Ya existe un producto llamado '{$existente->name}' (ID: {$existente->id}) con precio \$" . 
                                number_format($existente->sale_price, 0, ',', '.') . 
                                " y stock de {$existente->current_stock} unidades. ¿Deseas actualizar el existente o crear uno nuevo con diferente nombre?"
                ];
            }

            // 2. Buscar o crear categoría
            $categoria = null;
            if (!empty($categoriaNombre)) {
                // Buscar categoría por nombre (case insensitive)
                $categoria = Category::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($categoriaNombre) . '%'])
                    ->where('active', true)
                    ->first();
                
                // Si no existe, crearla
                if (!$categoria) {
                    $categoria = Category::create([
                        'name' => ucfirst($categoriaNombre),
                        'active' => true
                    ]);
                }
            } else {
                // Usar categoría por defecto
                $categoria = Category::where('active', true)->first();
                if (!$categoria) {
                    $categoria = Category::create([
                        'name' => 'General',
                        'active' => true
                    ]);
                }
            }

            // 3. Obtener Proveedor por defecto (o crear uno si no existe)
            $proveedor = Supplier::where('active', true)->first();
            if (!$proveedor) {
                $proveedor = Supplier::create([
                    'name' => 'Proveedor General',
                    'document' => '000000000',
                    'active' => true,
                    'email' => 'general@proveedor.com',
                    'phone' => '0000000000'
                ]);
            }

            // 4. Crear el producto en la base de datos del tenant actual
            $producto = Product::create([
                'name' => $nombre,
                'product_type' => 'simple',
                'sale_price' => $precio,
                'cost_price' => $costo,
                'current_stock' => $stock,
                'min_stock' => 5, // Valor por defecto razonable
                'manage_stock' => true,
                'active' => true,
                'category_id' => $categoria->id,
                'supplier_id' => $proveedor->id,
                'sku' => 'SKU-' . strtoupper(substr(md5($nombre . time()), 0, 8)),
                'barcode' => strval(rand(1000000000000, 9999999999999)), // EAN-13
            ]);

            // Log de auditoría

            $precioFormateado = '$' . number_format($producto->sale_price, 0, ',', '.');
            $costoFormateado = '$' . number_format($producto->cost_price, 0, ',', '.');
            $margen = $precio > 0 ? round((($precio - $costo) / $precio) * 100) : 0;

            return [
                'status' => 'ok',
                'product_id' => $producto->id,
                'message' => "✅ Producto creado exitosamente:\n\n" .
                            "📦 **{$producto->name}**\n" .
                            "• Precio de venta: {$precioFormateado}\n" .
                            "• Costo: {$costoFormateado} (margen: {$margen}%)\n" .
                            "• Stock inicial: {$producto->current_stock} unidades\n" .
                            "• Categoría: {$categoria->name}\n" .
                            "• SKU: {$producto->sku}"
            ];

        } catch (\Exception $e) {
            Log::error("❌ Error al crear producto por IA: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'No pude crear el producto. ' . $this->formatErrorMessage($e->getMessage())
            ];
        }
    }

    /**
     * Formatea mensajes de error para el usuario (sin tecnicismos)
     */
    private function formatErrorMessage($errorMessage)
    {
        // Ocultar detalles técnicos y dar mensajes amigables
        if (str_contains($errorMessage, 'Duplicate entry')) {
            return 'Ya existe un registro con esos datos.';
        }
        if (str_contains($errorMessage, 'Connection refused')) {
            return 'Hay un problema de conexión. Por favor intenta en unos segundos.';
        }
        if (str_contains($errorMessage, 'SQLSTATE')) {
            return 'Hubo un problema al guardar los datos. Por favor intenta de nuevo.';
        }
        return 'Por favor intenta de nuevo o contacta soporte si el problema persiste.';
    }

    private function consultarInventarioDB($data)
    {
        try {
            $query = trim($data['query'] ?? '');

            if (empty($query)) {
                return [
                    'results' => [],
                    'count' => 0,
                    'message' => "Por favor, indícame qué producto deseas buscar."
                ];
            }

            $productos = Product::where('active', true)
                ->where(function($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('sku', 'like', "%{$query}%")
                      ->orWhere('barcode', 'like', "%{$query}%");
                })
                ->orderBy('name')
                ->limit(15)
                ->get(['id', 'name', 'sku', 'sale_price', 'current_stock', 'category_id']);

            if ($productos->isEmpty()) {
                return [
                    'results' => [],
                    'count' => 0,
                    'encontrados' => false,
                    'message' => "No encontré ningún producto que coincida con '{$query}'. Verifica el nombre o intenta con otra palabra clave."
                ];
            }

            // Formatear resultados con precios en formato colombiano
            $resultadosFormateados = $productos->map(function($p) {
                return [
                    'id' => $p->id,
                    'nombre' => $p->name,
                    'sku' => $p->sku,
                    'precio' => '$' . number_format($p->sale_price, 0, ',', '.'),
                    'precio_raw' => $p->sale_price,
                    'stock' => intval($p->current_stock) . ' unidades'
                ];
            })->toArray();

            $mensajeAmbiguedad = '';
            if ($productos->count() > 1) {
                $mensajeAmbiguedad = " Si necesitas información de uno específico, indícame cuál.";
            }

            return [
                'results' => $resultadosFormateados,
                'count' => $productos->count(),
                'encontrados' => true,
                'multiple_results' => $productos->count() > 1,
                'message' => "Encontré {$productos->count()} producto(s) con '{$query}'.{$mensajeAmbiguedad}"
            ];

        } catch (\Exception $e) {
            Log::error("❌ Error al consultar inventario: " . $e->getMessage());
            return [
                'status' => 'error',
                'count' => 0,
                'message' => 'Tuve un problema al buscar en el inventario. Por favor intenta de nuevo.'
            ];
        }
    }

    private function obtenerEstadisticasDB()
    {
        try {
            // Estadísticas de inventario
            $totalProductos = Product::where('active', true)->count();
            $productosInactivos = Product::where('active', false)->count();
            $valorInventario = Product::where('active', true)->sum(DB::raw('sale_price * current_stock'));
            $costoInventario = Product::where('active', true)->sum(DB::raw('cost_price * current_stock'));
            $bajoStock = Product::where('active', true)->whereColumn('current_stock', '<=', 'min_stock')->count();
            $sinStock = Product::where('active', true)->where('current_stock', '<=', 0)->count();

            // Ventas de hoy
            $ventasHoy = Invoice::where('type', 'invoice')
                ->where('status', '!=', 'cancelled')
                ->whereDate('date', Carbon::today())
                ->sum('total');
            
            $facturasHoy = Invoice::where('type', 'invoice')
                ->where('status', '!=', 'cancelled')
                ->whereDate('date', Carbon::today())
                ->count();

            // Ventas de ayer para comparación
            $ventasAyer = Invoice::where('type', 'invoice')
                ->where('status', '!=', 'cancelled')
                ->whereDate('date', Carbon::yesterday())
                ->sum('total');

            // Calcular variación
            $variacion = $ventasAyer > 0 ? round((($ventasHoy - $ventasAyer) / $ventasAyer) * 100, 1) : 0;
            $variacionTexto = $variacion > 0 ? "+{$variacion}%" : "{$variacion}%";
            $variacionEmoji = $variacion >= 0 ? '📈' : '📉';

            // Formatear valores en formato colombiano
            $valorInventarioFmt = '$' . number_format($valorInventario, 0, ',', '.');
            $ventasHoyFmt = '$' . number_format($ventasHoy, 0, ',', '.');
            $ventasAyerFmt = '$' . number_format($ventasAyer, 0, ',', '.');

            // Determinar saludo según hora
            $hora = intval(now()->format('H'));
            $saludo = $hora >= 5 && $hora < 12 ? 'Buenos días' : ($hora >= 12 && $hora < 18 ? 'Buenas tardes' : 'Buenas noches');

            return [
                'total_productos' => $totalProductos,
                'productos_inactivos' => $productosInactivos,
                'valor_inventario' => $valorInventario,
                'valor_inventario_formateado' => $valorInventarioFmt,
                'costo_inventario' => $costoInventario,
                'productos_bajo_stock' => $bajoStock,
                'productos_sin_stock' => $sinStock,
                'ventas_hoy' => $ventasHoy,
                'ventas_hoy_formateado' => $ventasHoyFmt,
                'facturas_hoy' => $facturasHoy,
                'ventas_ayer' => $ventasAyer,
                'variacion_vs_ayer' => $variacionTexto,
                'mensaje' => "{$saludo}! 📊 Aquí está el resumen de tu negocio:\n\n" .
                            "📦 **Inventario**\n" .
                            "• {$totalProductos} productos activos\n" .
                            "• Valor total: {$valorInventarioFmt}\n" .
                            ($bajoStock > 0 ? "• ⚠️ {$bajoStock} productos con stock bajo\n" : "") .
                            ($sinStock > 0 ? "• ❌ {$sinStock} productos sin stock\n" : "") .
                            "\n💰 **Ventas de Hoy**\n" .
                            "• Total: {$ventasHoyFmt} ({$facturasHoy} facturas)\n" .
                            "• Ayer: {$ventasAyerFmt}\n" .
                            "• {$variacionEmoji} Variación: {$variacionTexto}"
            ];
        } catch (\Exception $e) {
            Log::error("❌ Error obtenerEstadisticas: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Tuve un problema al obtener las estadísticas. Por favor intenta de nuevo.'];
        }
    }

    private function actualizarProductoDB($data)
    {
        try {
            $nombre = trim($data['nombre'] ?? '');
            
            if (empty($nombre)) {
                return ['status' => 'error', 'message' => "Necesito saber qué producto deseas actualizar."];
            }

            // Buscar productos que coincidan
            $productos = Product::where('active', true)
                ->where(function($q) use ($nombre) {
                    $q->where('name', 'LIKE', "%{$nombre}%")
                      ->orWhere('sku', 'LIKE', "%{$nombre}%");
                })
                ->limit(5)
                ->get(['id', 'name', 'sku', 'sale_price', 'current_stock']);

            if ($productos->isEmpty()) {
                return [
                    'status' => 'not_found', 
                    'message' => "No encontré ningún producto con '{$nombre}'. Verifica el nombre e intenta de nuevo."
                ];
            }

            // REGLA DE AMBIGÜEDAD: Si hay múltiples resultados, listarlos
            if ($productos->count() > 1) {
                $lista = $productos->map(function($p, $i) {
                    return ($i + 1) . ") {$p->name} - \$" . number_format($p->sale_price, 0, ',', '.') . " - Stock: {$p->current_stock}";
                })->join("\n");

                return [
                    'status' => 'ambiguous',
                    'count' => $productos->count(),
                    'opciones' => $productos->toArray(),
                    'message' => "Encontré {$productos->count()} productos similares:\n{$lista}\n\n¿Cuál deseas actualizar? Indícame el nombre exacto."
                ];
            }

            // Solo un producto encontrado - proceder
            $producto = $productos->first();
            $cambios = [];
            $valoresAnteriores = [];

            // Guardar valores anteriores para trazabilidad
            if (isset($data['nuevo_precio'])) {
                $valoresAnteriores['precio'] = $producto->sale_price;
                $producto->sale_price = floatval($data['nuevo_precio']);
                $cambios[] = "precio: \$" . number_format($valoresAnteriores['precio'], 0, ',', '.') . " → \$" . number_format($data['nuevo_precio'], 0, ',', '.');
            }
            if (isset($data['nuevo_stock'])) {
                $valoresAnteriores['stock'] = $producto->current_stock;
                $producto->current_stock = intval($data['nuevo_stock']);
                $cambios[] = "stock: {$valoresAnteriores['stock']} → {$data['nuevo_stock']} unidades";
            }

            if (empty($cambios)) {
                return [
                    'status' => 'no_changes', 
                    'message' => "Encontré '{$producto->name}' pero no especificaste qué actualizar (precio o stock)."
                ];
            }

            $producto->save();

            // Log de auditoría

            return [
                'status' => 'ok',
                'product_id' => $producto->id,
                'message' => "✅ Producto '{$producto->name}' actualizado:\n• " . implode("\n• ", $cambios)
            ];

        } catch (\Exception $e) {
            Log::error("❌ Error actualizarProducto: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Tuve un problema al actualizar el producto. Por favor intenta de nuevo.'];
        }
    }

    private function eliminarProductoDB($data)
    {
        try {
            $nombre = trim($data['nombre'] ?? '');
            $confirmacion = $data['confirmacion'] ?? false;

            if (empty($nombre)) {
                return ['status' => 'error', 'message' => "Necesito saber qué producto deseas eliminar."];
            }

            // Buscar productos que coincidan
            $productos = Product::where('active', true)
                ->where(function($q) use ($nombre) {
                    $q->where('name', 'LIKE', "%{$nombre}%")
                      ->orWhere('name', $nombre); // Búsqueda exacta también
                })
                ->limit(5)
                ->get(['id', 'name', 'sku', 'sale_price', 'current_stock']);

            if ($productos->isEmpty()) {
                return [
                    'status' => 'not_found', 
                    'message' => "No encontré ningún producto con '{$nombre}' para eliminar."
                ];
            }

            // REGLA DE AMBIGÜEDAD: Si hay múltiples resultados, listarlos
            if ($productos->count() > 1) {
                $lista = $productos->map(function($p, $i) {
                    return ($i + 1) . ") {$p->name} (Stock: {$p->current_stock})";
                })->join("\n");

                return [
                    'status' => 'ambiguous',
                    'count' => $productos->count(),
                    'message' => "Encontré {$productos->count()} productos similares:\n{$lista}\n\n⚠️ Para eliminar, dime el nombre EXACTO del producto."
                ];
            }

            // Solo un producto encontrado
            $producto = $productos->first();

            // REGLA DE PROTECCIÓN: Verificar si el nombre coincide EXACTAMENTE
            // Si el usuario dijo "Coca" y el producto es "Coca-Cola 2L", pedir confirmación
            $nombreExacto = strtolower(trim($producto->name)) === strtolower(trim($nombre));

            if (!$nombreExacto && !$confirmacion) {
                return [
                    'status' => 'requires_confirmation',
                    'product_id' => $producto->id,
                    'product_name' => $producto->name,
                    'message' => "⚠️ ¿Confirmas que deseas eliminar '{$producto->name}'?\n\n" .
                                 "• Precio: \$" . number_format($producto->sale_price, 0, ',', '.') . "\n" .
                                 "• Stock actual: {$producto->current_stock} unidades\n\n" .
                                 "Esta acción NO se puede deshacer. Responde 'sí, eliminar {$producto->name}' para confirmar."
                ];
            }

            // Guardar información antes de eliminar para auditoría
            $productoInfo = [
                'id' => $producto->id,
                'name' => $producto->name,
                'sku' => $producto->sku,
                'sale_price' => $producto->sale_price,
                'current_stock' => $producto->current_stock
            ];

            // Desactivar en lugar de eliminar físicamente (mejor para auditoría DIAN)
            $producto->active = false;
            $producto->save();

            // Log de auditoría CRÍTICO
            Log::warning("🗑️ [IA Auditoría] Producto ELIMINADO (desactivado)", [
                'product' => $productoInfo,
                'user_id' => auth()->id(),
                'action' => 'soft_delete_via_ai',
                'timestamp' => now()->toISOString(),
                'ip' => request()->ip()
            ]);

            return [
                'status' => 'ok',
                'deleted_product' => $productoInfo,
                'message' => "✅ Producto '{$producto->name}' eliminado del inventario.\n\n" .
                            "📋 Registro guardado:\n" .
                            "• ID: {$productoInfo['id']}\n" .
                            "• Último precio: \$" . number_format($productoInfo['sale_price'], 0, ',', '.') . "\n" .
                            "• Stock que tenía: {$productoInfo['current_stock']} unidades"
            ];

        } catch (\Exception $e) {
            Log::error("❌ Error eliminarProducto: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Tuve un problema al eliminar el producto. Por favor intenta de nuevo.'];
        }
    }

    // ========================================
    // FUNCIONES DE BASE DE DATOS - VENTAS
    // ========================================

    private function consultarVentasDB($args)
    {
        try {
            $periodo = $args['periodo'] ?? null;
            $fecha = $args['fecha'] ?? null;
            $limite = intval($args['limite'] ?? 10);

            // Query para facturas PAGADAS (igual que la IA de voz)
            $query = Invoice::with('customer:id,name')
                ->where('type', 'invoice')
                ->where('status', 'paid'); // Solo facturas pagadas

            // Texto descriptivo del período
            $periodoTexto = 'todos los tiempos';

            // Filtrar por fecha específica
            if ($fecha) {
                try {
                    $fechaCarbon = Carbon::parse($fecha);
                    $query->whereDate('date', $fechaCarbon);
                    $periodoTexto = $fechaCarbon->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY');
                } catch (\Exception $e) {
                    return ['status' => 'error', 'message' => "La fecha '{$fecha}' no es válida. Usa formato YYYY-MM-DD (ej: 2026-01-25)."];
                }
            }
            // Filtrar por período
            elseif ($periodo) {
                switch ($periodo) {
                    case 'hoy':
                        $query->whereDate('date', Carbon::today());
                        $periodoTexto = 'hoy (' . Carbon::today()->format('d/m/Y') . ')';
                        break;
                    case 'ayer':
                        $query->whereDate('date', Carbon::yesterday());
                        $periodoTexto = 'ayer (' . Carbon::yesterday()->format('d/m/Y') . ')';
                        break;
                    case 'semana':
                        $query->where('date', '>=', Carbon::now()->startOfWeek());
                        $periodoTexto = 'esta semana';
                        break;
                    case 'mes':
                        $query->where('date', '>=', Carbon::now()->startOfMonth());
                        $periodoTexto = 'este mes (' . Carbon::now()->locale('es')->isoFormat('MMMM YYYY') . ')';
                        break;
                    case 'año':
                        $query->where('date', '>=', Carbon::now()->startOfYear());
                        $periodoTexto = 'este año (' . Carbon::now()->format('Y') . ')';
                        break;
                    case 'ultimos_7_dias':
                        $query->where('date', '>=', Carbon::now()->subDays(7));
                        $periodoTexto = 'los últimos 7 días';
                        break;
                    case 'ultimos_30_dias':
                        $query->where('date', '>=', Carbon::now()->subDays(30));
                        $periodoTexto = 'los últimos 30 días';
                        break;
                }
            }

            $totalVentas = (clone $query)->sum('total');
            $cantidadFacturas = (clone $query)->count();
            $ticketPromedio = $cantidadFacturas > 0 ? round($totalVentas / $cantidadFacturas, 0) : 0;

            $facturas = $query->orderBy('date', 'desc')
                ->limit($limite)
                ->get(['id', 'number', 'customer_id', 'date', 'total', 'status', 'payment_method'])
                ->map(function($f) {
                    return [
                        'id' => $f->id,
                        'numero' => $f->number,
                        'cliente' => $f->customer?->name ?? 'Cliente General',
                        'fecha' => Carbon::parse($f->date)->format('d/m/Y H:i'),
                        'total' => '$' . number_format($f->total, 0, ',', '.'),
                        'total_raw' => $f->total,
                        'metodo_pago' => $f->payment_method
                    ];
                });

            // Formatear valores
            $totalVentasFmt = '$' . number_format($totalVentas, 0, ',', '.');
            $ticketPromedioFmt = '$' . number_format($ticketPromedio, 0, ',', '.');

            // Construir mensaje según resultados
            if ($cantidadFacturas === 0) {
                return [
                    'periodo' => $periodoTexto,
                    'total_ventas' => 0,
                    'total_ventas_formateado' => '$0',
                    'cantidad_facturas' => 0,
                    'ticket_promedio' => 0,
                    'facturas' => [],
                    'hay_ventas' => false,
                    'mensaje' => "📊 No encontré ventas para {$periodoTexto}.\n\nEl período consultado no tiene facturas registradas."
                ];
            }

            // Construir lista de facturas para el mensaje
            $listaFacturas = $facturas->take(5)->map(function($f) {
                return "• {$f['numero']}: {$f['cliente']} - {$f['total']}";
            })->join("\n");

            $mensajeMasFacturas = $cantidadFacturas > 5 ? "\n...y " . ($cantidadFacturas - 5) . " facturas más." : "";

            return [
                'periodo' => $periodoTexto,
                'total_ventas' => $totalVentas,
                'total_ventas_formateado' => $totalVentasFmt,
                'cantidad_facturas' => $cantidadFacturas,
                'ticket_promedio' => $ticketPromedio,
                'ticket_promedio_formateado' => $ticketPromedioFmt,
                'facturas' => $facturas->toArray(),
                'hay_ventas' => true,
                'mensaje' => "💰 **Ventas de {$periodoTexto}**\n\n" .
                            "• Total: {$totalVentasFmt}\n" .
                            "• Facturas: {$cantidadFacturas}\n" .
                            "• Ticket promedio: {$ticketPromedioFmt}\n\n" .
                            "📋 **Últimas facturas:**\n{$listaFacturas}{$mensajeMasFacturas}"
            ];

        } catch (\Exception $e) {
            Log::error("❌ Error consultarVentas: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Tuve un problema al consultar las ventas. Por favor intenta de nuevo.'];
        }
    }

    private function obtenerReporteVentasDB($args)
    {
        try {
            $periodo = $args['periodo'] ?? 'hoy';

            $dateFilter = match($periodo) {
                'hoy' => Carbon::today(),
                'ayer' => Carbon::yesterday(),
                'semana' => Carbon::now()->startOfWeek(),
                'mes' => Carbon::now()->startOfMonth(),
                default => Carbon::today()
            };

            $isExactDate = in_array($periodo, ['hoy', 'ayer']);

            $queryBase = Invoice::where('type', 'invoice')
                ->where('status', '!=', 'cancelled');

            if ($isExactDate) {
                $queryBase->whereDate('date', $dateFilter);
            } else {
                $queryBase->where('date', '>=', $dateFilter);
            }

            $totalVentas = (clone $queryBase)->sum('total');
            $cantidadFacturas = (clone $queryBase)->count();
            $ticketPromedio = $cantidadFacturas > 0 ? round($totalVentas / $cantidadFacturas, 2) : 0;

            // Si hay pocas facturas (<= 10), incluirlas para dar contexto inmediato (quién compró, qué, etc.)
            $ultimasFacturas = [];
            if ($cantidadFacturas > 0 && $cantidadFacturas <= 10) {
                $ultimasFacturas = (clone $queryBase)
                    ->with('customer:id,name')
                    ->orderBy('date', 'desc')
                    ->get(['id', 'number', 'customer_id', 'date', 'total'])
                    ->map(function($f) {
                        return [
                            'id' => $f->id,
                            'numero' => $f->number,
                            'cliente' => $f->customer?->name ?? 'Cliente General',
                            'total' => $f->total,
                            'fecha' => $f->date->toDateTimeString()
                        ];
                    })
                    ->toArray();
            }

            // Productos más vendidos
            $topProductos = InvoiceItem::whereHas('invoice', function($q) use ($dateFilter, $isExactDate) {
                    $q->where('type', 'invoice')->where('status', '!=', 'cancelled');
                    if ($isExactDate) {
                        $q->whereDate('date', $dateFilter);
                    } else {
                        $q->where('date', '>=', $dateFilter);
                    }
                })
                ->selectRaw('product_name, SUM(quantity) as cantidad_vendida, SUM(unit_price * quantity) as total_vendido')
                ->groupBy('product_name')
                ->orderByDesc('cantidad_vendida')
                ->limit(5)
                ->get()
                ->toArray();

            return [
                'periodo' => $periodo,
                'total_ventas' => $totalVentas,
                'cantidad_facturas' => $cantidadFacturas,
                'ticket_promedio' => $ticketPromedio,
                'ultimas_facturas' => $ultimasFacturas, // Contexto extra para preguntas de seguimiento
                'productos_mas_vendidos' => $topProductos,
                'mensaje' => "Reporte de ventas para {$periodo}: {$cantidadFacturas} facturas, total $" . number_format($totalVentas, 0, ',', '.')
            ];

        } catch (\Exception $e) {
            Log::error("❌ Error obtenerReporteVentas: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error al generar reporte.'];
        }
    }

    private function obtenerProductosMenosVendidosDB($args)
    {
        try {
            $periodo = $args['periodo'] ?? 'mes';
            $limite = intval($args['limite'] ?? 10);

            $dateFilter = match($periodo) {
                'semana' => Carbon::now()->subWeek(),
                'mes' => Carbon::now()->subMonth(),
                'trimestre' => Carbon::now()->subMonths(3),
                'año' => Carbon::now()->subYear(),
                default => Carbon::now()->subMonth()
            };

            // Obtener todos los productos activos
            $productosActivos = Product::where('active', true)
                ->select('id', 'name', 'sku', 'sale_price', 'stock')
                ->get();

            // Obtener ventas por producto en el período
            $ventasPorProducto = InvoiceItem::whereHas('invoice', function($q) use ($dateFilter) {
                    $q->where('type', 'invoice')
                      ->where('status', '!=', 'cancelled')
                      ->where('date', '>=', $dateFilter);
                })
                ->selectRaw('product_id, SUM(quantity) as cantidad_vendida')
                ->groupBy('product_id')
                ->pluck('cantidad_vendida', 'product_id')
                ->toArray();

            // Combinar productos con sus ventas (o 0 si no vendieron)
            $productosConVentas = $productosActivos->map(function($producto) use ($ventasPorProducto) {
                return [
                    'id' => $producto->id,
                    'nombre' => $producto->name,
                    'sku' => $producto->sku,
                    'precio' => $producto->sale_price,
                    'stock_actual' => $producto->stock,
                    'cantidad_vendida' => $ventasPorProducto[$producto->id] ?? 0
                ];
            });

            // Ordenar por menor cantidad vendida y tomar el límite
            $menosVendidos = $productosConVentas
                ->sortBy('cantidad_vendida')
                ->take($limite)
                ->values()
                ->toArray();

            $sinVentas = collect($menosVendidos)->where('cantidad_vendida', 0)->count();

            return [
                'periodo' => $periodo,
                'productos_menos_vendidos' => $menosVendidos,
                'total_productos_analizados' => $productosActivos->count(),
                'productos_sin_ventas' => $sinVentas,
                'mensaje' => "Los {$limite} productos con menor rotación en el último {$periodo}. {$sinVentas} productos no han tenido ninguna venta."
            ];

        } catch (\Exception $e) {
            Log::error("❌ Error obtenerProductosMenosVendidos: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error al obtener productos menos vendidos.'];
        }
    }

    private function obtenerMejoresClientesDB($args)
    {
        try {
            $periodo = $args['periodo'] ?? 'mes';
            $limite = intval($args['limite'] ?? 10);

            $dateFilter = match($periodo) {
                'hoy' => Carbon::today(),
                'semana' => Carbon::now()->subWeek(),
                'mes' => Carbon::now()->subMonth(),
                'año' => Carbon::now()->subYear(),
                default => Carbon::now()->subMonth()
            };

            $isExactDate = $periodo === 'hoy';

            // Obtener compras por cliente
            $queryBase = Invoice::where('type', 'invoice')
                ->where('status', '!=', 'cancelled')
                ->whereNotNull('customer_id');

            if ($isExactDate) {
                $queryBase->whereDate('date', $dateFilter);
            } else {
                $queryBase->where('date', '>=', $dateFilter);
            }

            $mejoresClientes = $queryBase
                ->selectRaw('customer_id, COUNT(*) as total_compras, SUM(total) as monto_total')
                ->groupBy('customer_id')
                ->orderByDesc('monto_total')
                ->limit($limite)
                ->with('customer:id,name,email,phone')
                ->get()
                ->map(function($registro) {
                    return [
                        'cliente_id' => $registro->customer_id,
                        'nombre' => $registro->customer?->name ?? 'Cliente eliminado',
                        'email' => $registro->customer?->email ?? '-',
                        'telefono' => $registro->customer?->phone ?? '-',
                        'total_compras' => $registro->total_compras,
                        'monto_total' => round($registro->monto_total, 2)
                    ];
                })
                ->toArray();

            $montoTotal = collect($mejoresClientes)->sum('monto_total');

            return [
                'periodo' => $periodo,
                'mejores_clientes' => $mejoresClientes,
                'total_clientes_encontrados' => count($mejoresClientes),
                'monto_total_combinado' => $montoTotal,
                'mensaje' => "Los {$limite} mejores clientes del último {$periodo}. En total representan $" . number_format($montoTotal, 0, ',', '.') . " en compras."
            ];

        } catch (\Exception $e) {
            Log::error("❌ Error obtenerMejoresClientes: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error al obtener mejores clientes.'];
        }
    }

    // ========================================
    // FUNCIONES DE BASE DE DATOS - CLIENTES
    // ========================================

    private function consultarClientesDB($args)
    {
        try {
            $query = $args['query'] ?? '';
            $limite = $args['limite'] ?? 10;

            $clientesQuery = Customer::query();

            if ($query) {
                $clientesQuery->where(function($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('email', 'LIKE', "%{$query}%")
                      ->orWhere('phone', 'LIKE', "%{$query}%")
                      ->orWhere('document_number', 'LIKE', "%{$query}%");
                });
            }

            $clientes = $clientesQuery->limit($limite)
                ->get(['id', 'name', 'email', 'phone', 'document_number'])
                ->toArray();

            return [
                'clientes' => $clientes,
                'count' => count($clientes),
                'mensaje' => count($clientes) > 0
                    ? "Se encontraron " . count($clientes) . " clientes."
                    : "No se encontraron clientes con ese criterio."
            ];

        } catch (\Exception $e) {
            Log::error("❌ Error consultarClientes: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error al buscar clientes.'];
        }
    }

    // ========================================
    // FUNCIONES DE BASE DE DATOS - FACTURAS
    // ========================================

    private function consultarFacturasDB($args)
    {
        try {
            $cliente = $args['cliente'] ?? null;
            $numeroFactura = $args['numero_factura'] ?? null;
            $periodo = $args['periodo'] ?? null;
            $limite = $args['limite'] ?? 20;

            $query = Invoice::with('customer:id,name')
                ->where('type', 'invoice');

            if ($numeroFactura) {
                $query->where('number', 'LIKE', "%{$numeroFactura}%");
            }

            if ($cliente) {
                $query->whereHas('customer', function($q) use ($cliente) {
                    $q->where('name', 'LIKE', "%{$cliente}%");
                });
            }

            if ($periodo) {
                switch ($periodo) {
                    case 'hoy':
                        $query->whereDate('date', Carbon::today());
                        break;
                    case 'ayer':
                        $query->whereDate('date', Carbon::yesterday());
                        break;
                    case 'semana':
                        $query->where('date', '>=', Carbon::now()->startOfWeek());
                        break;
                    case 'mes':
                        $query->where('date', '>=', Carbon::now()->startOfMonth());
                        break;
                }
            }

            $facturas = $query->orderBy('date', 'desc')
                ->limit($limite)
                ->get(['id', 'number', 'customer_id', 'date', 'total', 'status', 'payment_method'])
                ->map(function($f) {
                    return [
                        'id' => $f->id,
                        'numero' => $f->number,
                        'cliente' => $f->customer?->name ?? 'Cliente General',
                        'fecha' => $f->date,
                        'total' => $f->total,
                        'estado' => $f->status,
                        'metodo_pago' => $f->payment_method
                    ];
                });

            return [
                'facturas' => $facturas->toArray(),
                'count' => $facturas->count(),
                'mensaje' => $facturas->count() > 0
                    ? "Se encontraron {$facturas->count()} facturas."
                    : "No se encontraron facturas con ese criterio."
            ];

        } catch (\Exception $e) {
            Log::error("❌ Error consultarFacturas: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error al buscar facturas.'];
        }
    }

    private function obtenerDetalleFacturaDB($args)
    {
        try {
            $facturaId = $args['factura_id'] ?? null;

            if (!$facturaId) {
                return ['status' => 'error', 'message' => 'Se requiere el ID de la factura.'];
            }

            $factura = Invoice::with(['customer', 'items'])
                ->where('type', 'invoice')
                ->find($facturaId);

            if (!$factura) {
                return ['status' => 'error', 'message' => "No se encontró la factura con ID {$facturaId}."];
            }

            $productos = $factura->items->map(function($item) {
                return [
                    'producto' => $item->product_name,
                    'cantidad' => $item->quantity,
                    'precio_unitario' => $item->unit_price,
                    'subtotal' => $item->quantity * $item->unit_price
                ];
            });

            return [
                'factura' => [
                    'id' => $factura->id,
                    'numero' => $factura->number,
                    'cliente' => $factura->customer?->name ?? 'Cliente General',
                    'fecha' => $factura->date,
                    'subtotal' => $factura->subtotal,
                    'impuesto' => $factura->tax,
                    'total' => $factura->total,
                    'estado' => $factura->status,
                    'metodo_pago' => $factura->payment_method
                ],
                'productos' => $productos->toArray(),
                'mensaje' => "Factura {$factura->number} del cliente " . ($factura->customer?->name ?? 'Cliente General') . " por $" . number_format($factura->total, 0, ',', '.')
            ];

        } catch (\Exception $e) {
            Log::error("❌ Error obtenerDetalleFactura: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error al obtener detalles de la factura.'];
        }
    }

    /**
     * Obtiene la última factura generada en el sistema
     */
    private function obtenerUltimaFacturaDB()
    {
        try {
            $factura = Invoice::with(['customer', 'items'])
                ->where('type', 'invoice')
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$factura) {
                return [
                    'status' => 'not_found',
                    'message' => 'No hay facturas registradas en el sistema.'
                ];
            }

            $productos = $factura->items->map(function($item) {
                return [
                    'producto' => $item->product_name,
                    'cantidad' => $item->quantity,
                    'precio_unitario' => $item->unit_price,
                    'subtotal' => $item->quantity * $item->unit_price
                ];
            });

            return [
                'factura' => [
                    'id' => $factura->id,
                    'numero' => $factura->number,
                    'cliente' => $factura->customer?->name ?? 'Cliente General',
                    'vendedor' => $factura->seller_name ?? 'No registrado',
                    'fecha' => Carbon::parse($factura->date)->format('d/m/Y'),
                    'hora' => Carbon::parse($factura->created_at)->format('H:i'),
                    'subtotal' => $factura->subtotal,
                    'impuesto' => $factura->tax_amount ?? 0,
                    'total' => $factura->total,
                    'estado' => $factura->status,
                    'metodo_pago' => $factura->payment_method ?? 'Efectivo'
                ],
                'productos' => $productos->toArray(),
                'cantidad_productos' => $productos->count(),
                'mensaje' => "La última factura es {$factura->number} del " . Carbon::parse($factura->date)->format('d/m/Y') . 
                             " a las " . Carbon::parse($factura->created_at)->format('H:i') .
                             " para " . ($factura->customer?->name ?? 'Cliente General') . 
                             " por $" . number_format($factura->total, 0, ',', '.') .
                             " con {$productos->count()} producto(s)."
            ];

        } catch (\Exception $e) {
            Log::error("❌ Error obtenerUltimaFactura: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error al obtener la última factura.'];
        }
    }

    // ========================================
    // FUNCIONES DE BASE DE DATOS - CATEGORÍAS
    // ========================================

    private function consultarCategoriasDB($args)
    {
        try {
            $query = $args['query'] ?? '';

            $categoriasQuery = Category::query();

            if ($query) {
                $categoriasQuery->where('name', 'LIKE', "%{$query}%");
            }

            $categorias = $categoriasQuery->limit(20)
                ->get(['id', 'name', 'description'])
                ->toArray();

            return [
                'categorias' => $categorias,
                'count' => count($categorias),
                'mensaje' => count($categorias) > 0
                    ? "Se encontraron " . count($categorias) . " categorías."
                    : "No se encontraron categorías."
            ];

        } catch (\Exception $e) {
            Log::error("❌ Error consultarCategorias: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error al buscar categorías.'];
        }
    }

    // ========================================
    // FUNCIONES DE BASE DE DATOS - PREMIUM
    // ========================================

    private function consultarDeudasClientesDB($args)
    {
        try {
            $minimo = $args['minimo_deuda'] ?? 0;
            $limite = $args['limite'] ?? 10;

            $clientes = Customer::where('current_debt', '>', $minimo)
                ->orderByDesc('current_debt')
                ->limit($limite)
                ->get(['id', 'name', 'current_debt', 'phone', 'credit_limit'])
                ->map(function($c) {
                    return [
                        'nombre' => $c->name,
                        'deuda_actual' => $c->current_debt,
                        'limite_credito' => $c->credit_limit,
                        'telefono' => $c->phone
                    ];
                });

            $totalDeuda = Customer::sum('current_debt');
            $countDeudores = Customer::where('current_debt', '>', 0)->count();

            return [
                'clientes_deudores' => $clientes->toArray(),
                'total_deuda_global' => $totalDeuda,
                'cantidad_deudores' => $countDeudores,
                'mensaje' => $countDeudores > 0
                    ? "Hay {$countDeudores} clientes con deuda total de $" . number_format($totalDeuda, 0, ',', '.')
                    : "No hay clientes con deudas pendientes."
            ];

        } catch (\Exception $e) {
            Log::error("❌ Error consultarDeudasClientes: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error al consultar deudas.'];
        }
    }

    private function consultarSedesDB()
    {
        try {
            // Verificar si existe el modelo Warehouse
            if (!class_exists('\App\Models\Warehouse')) {
                return ['status' => 'error', 'message' => 'El módulo de Sedes no está instalado o activo.'];
            }

            $sedes = \App\Models\Warehouse::where('active', true)
                ->get(['id', 'name', 'address', 'phone', 'is_default'])
                ->toArray();

            return [
                'sedes' => $sedes,
                'count' => count($sedes),
                'mensaje' => count($sedes) > 0
                    ? "Se encontraron " . count($sedes) . " sedes activas."
                    : "No se encontraron sedes registradas."
            ];

        } catch (\Exception $e) {
            Log::error("❌ Error consultarSedes: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error al consultar sedes.'];
        }
    }

    private function crearSedeDB($args)
    {
        try {
            // Verificar si existe el modelo Warehouse
            if (!class_exists('\App\Models\Warehouse')) {
                return ['status' => 'error', 'message' => 'El módulo de Sedes no está instalado o activo.'];
            }

            $nombre = $args['nombre'] ?? null;
            $direccion = $args['direccion'] ?? null;
            $telefono = $args['telefono'] ?? null;

            if (!$nombre) {
                return ['status' => 'error', 'message' => 'El nombre de la sede es obligatorio.'];
            }

            // Validar límite de tiendas según plan
            $tenantPlan = tenant('plan') ?? 'free_trial';
            $warehouseCount = \App\Models\Warehouse::count();

            // Planes que NO pueden usar multi-tienda
            if (in_array($tenantPlan, ['free_trial', 'basic'])) {
                return [
                    'status' => 'error',
                    'message' => "Tu plan actual ({$tenantPlan}) no permite crear múltiples sedes. Necesitas el plan Premium o Enterprise."
                ];
            }

            // Premium: máximo 3 tiendas
            if ($tenantPlan === 'premium' && $warehouseCount >= 3) {
                return [
                    'status' => 'error',
                    'message' => "Has alcanzado el límite de 3 sedes para tu plan Premium. Actualiza a Enterprise para crear más."
                ];
            }

            // Crear la sede
            $sede = \App\Models\Warehouse::create([
                'name' => $nombre,
                'address' => $direccion,
                'phone' => $telefono,
                'active' => true,
                'is_default' => false // Las creadas por IA no son default por seguridad
            ]);

            return [
                'status' => 'success',
                'message' => "Sede '{$sede->name}' creada exitosamente con ID {$sede->id}.",
                'sede' => $sede
            ];

        } catch (\Exception $e) {
            Log::error("❌ Error crearSede: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error al crear la sede: ' . $e->getMessage()];
        }
    }

    // ========================================
    // FUNCIONES DE BASE DE DATOS - IMPORTACIÓN
    // ========================================

    private function analizarArchivoProductosDB($args)
    {
        try {
            $filePath = $args['file_path'] ?? null;

            if (!$filePath) {
                return [
                    'status' => 'error',
                    'message' => 'No se proporcionó la ruta del archivo.'
                ];
            }

            // Verificar si el archivo existe
            if (!file_exists($filePath)) {
                return [
                    'status' => 'error',
                    'message' => 'El archivo no existe en la ruta especificada: ' . $filePath
                ];
            }


            // Usar los servicios de análisis existentes
            $excelParser = new \App\Services\ExcelParserService();
            $columnMapper = new \App\Services\AIColumnMapperService();

            // Parsear el archivo
            $parseResult = $excelParser->parseFile($filePath);


            // Analizar con IA
            $aiAnalysis = $columnMapper->analyzeColumnsWithAI(
                $parseResult['headers'],
                $parseResult['sample_data']
            );


            // Preparar resumen para Gemini
            $mapping = $aiAnalysis['column_mapping'];
            $mappingText = [];
            foreach ($mapping as $excelCol => $systemField) {
                if ($systemField !== 'ignore') {
                    $mappingText[] = "• '$excelCol' → $systemField";
                }
            }

            return [
                'status' => 'success',
                'archivo' => basename($filePath),
                'total_filas' => $parseResult['total_rows'],
                'total_columnas' => count($parseResult['headers']),
                'columnas_detectadas' => $parseResult['headers'],
                'mapeo_inteligente' => $mapping,
                'confianza_ia' => $aiAnalysis['confidence'] . '%',
                'metodo_analisis' => $aiAnalysis['method'],
                'advertencias' => $aiAnalysis['warnings'] ?? [],
                'muestra_datos' => array_slice($parseResult['sample_data'], 0, 3),
                'mensaje' => "📊 Archivo analizado exitosamente:\n\n" .
                    "✅ {$parseResult['total_rows']} productos detectados\n" .
                    "✅ Confianza del análisis: {$aiAnalysis['confidence']}%\n\n" .
                    "🔄 Mapeo automático de columnas:\n" . implode("\n", $mappingText) .
                    "\n\n💡 El sistema está listo para importar estos productos a la base de datos."
            ];

        } catch (\Exception $e) {
            Log::error("❌ Error analizarArchivoProductos: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'status' => 'error',
                'message' => 'Error al analizar el archivo: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Importación masiva de productos desde JSON
     * Acepta headers y rows para crear productos en bulk
     */
    private function importarProductosMasivoHandler($args)
    {
        try {
            $productsData = $args['products_data'] ?? null;
            $warehouseId = $args['warehouse_id'] ?? null; // Bodega específica (opcional)

            if (!$productsData || !isset($productsData['headers']) || !isset($productsData['rows'])) {
                return [
                    'status' => 'error',
                    'message' => 'Datos incompletos. Se requiere products_data con headers y rows.'
                ];
            }

            $headers = $productsData['headers'];
            $rows = $productsData['rows'];

            // Obtener bodega de destino
            if ($warehouseId) {
                $targetWarehouse = \App\Models\Warehouse::find($warehouseId);
                if (!$targetWarehouse) {
                    return [
                        'status' => 'error',
                        'message' => "Bodega con ID {$warehouseId} no encontrada."
                    ];
                }
            } else {
                // Usar bodega por defecto
                $targetWarehouse = \App\Models\Warehouse::where('is_default', 1)->first();
                if (!$targetWarehouse) {
                    $targetWarehouse = \App\Models\Warehouse::first();
                }

                if (!$targetWarehouse) {
                    return [
                        'status' => 'error',
                        'message' => 'No hay bodegas configuradas en el sistema.'
                    ];
                }
            }


            // Mapear headers a índices
            $headerMap = [];
            foreach ($headers as $index => $header) {
                $headerMap[strtolower(trim($header))] = $index;
            }


            $createdCount = 0;
            $errors = [];
            $categoriesCache = [];

            foreach ($rows as $rowIndex => $row) {
                try {
                    // Extraer datos de la fila (con más variantes de nombres de columnas)
                    $nombre = $this->getValueFromRow($row, $headerMap, ['nombre', 'producto', 'name', 'nombre del producto', 'nombre producto', 'descripcion', 'descripción', 'articulo', 'artículo', 'item']);
                    $precioVenta = $this->getValueFromRow($row, $headerMap, ['precio venta', 'precio', 'price', 'venta', 'precioventa', 'precio_venta', 'pvp', 'precio unitario']);
                    $precioCosto = $this->getValueFromRow($row, $headerMap, ['precio costo', 'costo', 'cost', 'precio compra', 'preciocompra', 'precio_costo', 'costo unitario', 'compra']);
                    $stock = $this->getValueFromRow($row, $headerMap, ['stock', 'cantidad', 'existencia', 'inventario', 'unidades', 'qty', 'existencias']);
                    $sku = $this->getValueFromRow($row, $headerMap, ['sku', 'código', 'code', 'codigo', 'referencia', 'ref']);
                    $barcode = $this->getValueFromRow($row, $headerMap, ['código barras', 'barcode', 'ean', 'upc', 'codigo de barras', 'códigobarras']);
                    $categoriaNombre = $this->getValueFromRow($row, $headerMap, ['categoría', 'categoria', 'category', 'tipo', 'grupo', 'familia']);

                    // Validar datos mínimos
                    if (empty($nombre)) {
                        $errors[] = "Fila " . ($rowIndex + 1) . ": nombre vacío, omitido.";
                        continue;
                    }

                    // Obtener o crear categoría
                    $categoryId = null;
                    if (!empty($categoriaNombre)) {
                        if (isset($categoriesCache[$categoriaNombre])) {
                            $categoryId = $categoriesCache[$categoriaNombre];
                        } else {
                            $category = Category::firstOrCreate(
                                ['name' => $categoriaNombre],
                                ['active' => true]
                            );
                            $categoryId = $category->id;
                            $categoriesCache[$categoriaNombre] = $categoryId;
                        }
                    } else {
                        // Categoría por defecto
                        $defaultCategory = Category::first();
                        if (!$defaultCategory) {
                            $defaultCategory = Category::create([
                                'name' => 'General',
                                'active' => true
                            ]);
                        }
                        $categoryId = $defaultCategory->id;
                    }

                    // Generar SKU automático si no viene
                    $productSku = $sku;
                    if (empty($productSku)) {
                        // Generar SKU basado en nombre + timestamp + random
                        $productSku = 'SKU-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $nombre), 0, 5)) . '-' . substr(time(), -5) . rand(10, 99);
                    }
                    
                    // Generar barcode vacío si no viene (puede ser null en DB normalmente)
                    $productBarcode = $barcode ?: null;

                    // Crear producto (sin stock en products, se maneja en product_warehouse)
                    $product = Product::create([
                        'name' => $nombre,
                        'product_type' => 'simple',
                        'sale_price' => floatval($precioVenta ?? 0),
                        'cost_price' => floatval($precioCosto ?? 0),
                        'current_stock' => intval($stock ?? 0), // Total del stock
                        'sku' => $productSku,
                        'barcode' => $productBarcode,
                        'category_id' => $categoryId,
                        'active' => true,
                    ]);

                    // ✅ ASIGNAR STOCK A BODEGA (Multisede)
                    if ($targetWarehouse) {
                        $product->warehouses()->attach($targetWarehouse->id, [
                            'stock' => intval($stock ?? 0),
                            'product_variant_id' => null
                        ]);

                    }

                    $createdCount++;


                } catch (\Exception $e) {
                    $errors[] = "Fila " . ($rowIndex + 1) . ": " . $e->getMessage();
                    Log::error('[GeminiAgent] Error en fila ' . ($rowIndex + 1), [
                        'error' => $e->getMessage(),
                        'row' => $row
                    ]);
                }
            }

            $result = [
                'status' => 'success',
                'productos_creados' => $createdCount,
                'total_filas' => count($rows),
                'bodega' => $targetWarehouse->name,
                'errores_count' => count($errors),
                'errores' => $errors,
                'mensaje' => "✅ Importación completada:\n\n" .
                    "• {$createdCount} productos creados exitosamente\n" .
                    "• " . count($rows) . " filas procesadas\n" .
                    "• Stock asignado a: {$targetWarehouse->name}\n" .
                    (count($errors) > 0 ? "• " . count($errors) . " errores encontrados\n" : "") .
                    "\n📦 Los productos ya están disponibles en tu inventario."
            ];


            return $result;

        } catch (\Exception $e) {
            Log::error("❌ Error importarProductosMasivo: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'status' => 'error',
                'message' => 'Error al importar productos: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Helper para extraer valor de una fila usando múltiples nombres posibles
     */
    private function getValueFromRow($row, $headerMap, $possibleNames)
    {
        foreach ($possibleNames as $name) {
            $key = strtolower(trim($name));
            if (isset($headerMap[$key])) {
                $index = $headerMap[$key];
                if (isset($row[$index])) {
                    return trim($row[$index]);
                }
            }
        }
        return null;
    }

    // ========================================
    // NUEVAS FUNCIONES INTELIGENTES
    // ========================================

    /**
     * Lista TODOS los productos sin necesidad de búsqueda
     */
    private function listarProductosDB($args)
    {
        try {
            $limite = intval($args['limite'] ?? 20);
            $ordenarPor = $args['ordenar_por'] ?? 'nombre';
            $soloActivos = $args['solo_activos'] ?? true;

            $query = Product::query();

            if ($soloActivos) {
                $query->where('active', true);
            }

            // Ordenar
            switch ($ordenarPor) {
                case 'precio':
                    $query->orderBy('sale_price', 'desc');
                    break;
                case 'stock':
                    $query->orderBy('current_stock', 'desc');
                    break;
                case 'ventas':
                    $query->withCount(['invoiceItems as total_vendido' => function($q) {
                        $q->select(DB::raw('COALESCE(SUM(quantity), 0)'));
                    }])->orderBy('total_vendido', 'desc');
                    break;
                default:
                    $query->orderBy('name', 'asc');
            }

            $totalProductos = Product::where('active', true)->count();
            $productos = $query->limit($limite)->get(['id', 'name', 'sku', 'sale_price', 'cost_price', 'current_stock']);

            if ($productos->isEmpty()) {
                return [
                    'status' => 'empty',
                    'count' => 0,
                    'message' => 'No tienes productos registrados aún. ¿Quieres que te ayude a crear el primero?'
                ];
            }

            $lista = $productos->map(function($p, $i) {
                $margen = $p->cost_price > 0 
                    ? round((($p->sale_price - $p->cost_price) / $p->sale_price) * 100, 1) 
                    : 0;
                return [
                    'id' => $p->id,
                    'nombre' => $p->name,
                    'precio' => '$' . number_format($p->sale_price, 0, ',', '.'),
                    'stock' => intval($p->current_stock),
                    'margen' => $margen . '%'
                ];
            })->toArray();

            return [
                'status' => 'success',
                'total_inventario' => $totalProductos,
                'mostrando' => count($lista),
                'productos' => $lista,
                'message' => "Tienes {$totalProductos} productos en tu inventario. Mostrando los primeros {$limite}."
            ];

        } catch (\Exception $e) {
            Log::error("❌ Error listarProductos: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'No pude obtener la lista de productos.'];
        }
    }

    /**
     * Consulta configuración del sistema
     */
    private function consultarConfiguracionDB($args)
    {
        try {
            $modulo = $args['modulo'] ?? null;
            $settings = \App\Models\SystemSetting::getSettings();

            // Obtener plan del tenant
            $tenantPlan = 'free_trial';
            try {
                $tenantPlan = \DB::connection('mysql')
                    ->table('tenants')
                    ->where('id', tenant('id'))
                    ->value('plan') ?? 'free_trial';
            } catch (\Exception $e) {}

            $isPremium = in_array($tenantPlan, ['premium', 'enterprise']);

            $config = [
                'plan_actual' => $tenantPlan,
                'es_premium' => $isPremium,
                'modulos' => [
                    'creditienda' => [
                        'activo' => (bool) $settings->creditienda_enabled,
                        'disponible_en_plan' => $isPremium,
                        'descripcion' => 'Sistema de créditos para clientes (vender fiado)'
                    ],
                    'descuentos' => [
                        'activo' => (bool) $settings->discounts_enabled,
                        'disponible_en_plan' => true,
                        'descripcion' => 'Descuentos generales en productos'
                    ],
                    'descuentos_cliente' => [
                        'activo' => (bool) $settings->customer_discounts_enabled,
                        'disponible_en_plan' => true,
                        'descripcion' => 'Descuentos específicos por cliente'
                    ],
                    'codigos_promo' => [
                        'activo' => (bool) $settings->promo_codes_enabled,
                        'disponible_en_plan' => true,
                        'descripcion' => 'Códigos promocionales'
                    ],
                    'fidelizacion' => [
                        'activo' => (bool) $settings->enable_loyalty_system,
                        'disponible_en_plan' => $isPremium,
                        'descripcion' => 'Sistema de puntos de fidelización'
                    ],
                    'iva' => [
                        'activo' => (bool) $settings->iva_enabled,
                        'porcentaje' => $settings->iva_percentage ?? 19,
                        'disponible_en_plan' => true,
                        'descripcion' => 'Impuesto al Valor Agregado'
                    ]
                ],
                'configuracion_general' => [
                    'nombre_empresa' => $settings->company_name,
                    'tipo_tienda' => $settings->store_type,
                    'requiere_cliente' => (bool) $settings->require_customer,
                    'alertas_stock_bajo' => (bool) $settings->low_stock_alerts,
                    'umbral_stock_bajo' => $settings->low_stock_threshold
                ]
            ];

            // Si pidió un módulo específico
            if ($modulo && isset($config['modulos'][$modulo])) {
                $mod = $config['modulos'][$modulo];
                $estado = $mod['activo'] ? '✅ Activo' : '❌ Desactivado';
                $disponible = $mod['disponible_en_plan'] ? '' : ' (Requiere plan Premium)';

                return [
                    'status' => 'success',
                    'modulo' => $modulo,
                    'configuracion' => $mod,
                    'message' => "{$modulo}: {$estado}{$disponible}. {$mod['descripcion']}."
                ];
            }

            // Construir mensaje resumido
            $modulosActivos = [];
            $modulosInactivos = [];
            foreach ($config['modulos'] as $nombre => $mod) {
                if ($mod['activo']) {
                    $modulosActivos[] = $nombre;
                } else {
                    $modulosInactivos[] = $nombre;
                }
            }

            $mensaje = "📊 **Estado de tu configuración:**\n\n";
            $mensaje .= "Plan: " . strtoupper($tenantPlan) . "\n\n";
            $mensaje .= "✅ Activos: " . (count($modulosActivos) > 0 ? implode(', ', $modulosActivos) : 'Ninguno') . "\n";
            $mensaje .= "❌ Inactivos: " . (count($modulosInactivos) > 0 ? implode(', ', $modulosInactivos) : 'Ninguno') . "\n";

            if (!$isPremium && (in_array('creditienda', $modulosInactivos) || in_array('fidelizacion', $modulosInactivos))) {
                $mensaje .= "\n💡 Creditienda y Fidelización requieren plan Premium.";
            }

            return [
                'status' => 'success',
                'configuracion' => $config,
                'message' => $mensaje
            ];

        } catch (\Exception $e) {
            Log::error("❌ Error consultarConfiguracion: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'No pude consultar la configuración.'];
        }
    }

    /**
     * Actualiza configuración del sistema (activa/desactiva módulos)
     */
    private function actualizarConfiguracionDB($args)
    {
        try {
            $modulo = $args['modulo'] ?? null;
            $activar = $args['activar'] ?? false;

            if (!$modulo) {
                return ['status' => 'error', 'message' => 'Necesito saber qué módulo quieres modificar.'];
            }

            $settings = \App\Models\SystemSetting::first();
            if (!$settings) {
                $settings = \App\Models\SystemSetting::create([]);
            }

            // Obtener plan del tenant
            $tenantPlan = 'free_trial';
            try {
                $tenantPlan = \DB::connection('mysql')
                    ->table('tenants')
                    ->where('id', tenant('id'))
                    ->value('plan') ?? 'free_trial';
            } catch (\Exception $e) {}

            $isPremium = in_array($tenantPlan, ['premium', 'enterprise']);

            // Mapeo de módulos a columnas
            $moduloColumna = [
                'creditienda' => 'creditienda_enabled',
                'descuentos' => 'discounts_enabled',
                'fidelizacion' => 'enable_loyalty_system',
                'iva' => 'iva_enabled',
            ];

            if (!isset($moduloColumna[$modulo])) {
                return ['status' => 'error', 'message' => "No reconozco el módulo '{$modulo}'."];
            }

            // Verificar restricciones de plan
            $modulosPremium = ['creditienda', 'fidelizacion'];
            if (in_array($modulo, $modulosPremium) && !$isPremium && $activar) {
                return [
                    'status' => 'plan_required',
                    'message' => "⚠️ {$modulo} solo está disponible en planes Premium o Enterprise. " .
                                "Tu plan actual es {$tenantPlan}. ¿Quieres que te muestre cómo actualizar tu plan?"
                ];
            }

            // Actualizar
            $columna = $moduloColumna[$modulo];
            $estadoAnterior = $settings->$columna;
            $settings->$columna = $activar;
            $settings->save();

            $accion = $activar ? 'activado' : 'desactivado';
            $emoji = $activar ? '✅' : '❌';


            $mensajeSeguimiento = '';
            if ($activar && $modulo === 'creditienda') {
                $mensajeSeguimiento = "\n\n¿Te ayudo a crear tu primer crédito para un cliente o prefieres hacerlo manual desde el módulo?";
            } elseif ($activar && $modulo === 'fidelizacion') {
                $mensajeSeguimiento = "\n\n¿Quieres que configure los puntos de fidelización o lo hacemos después?";
            }

            return [
                'status' => 'success',
                'modulo' => $modulo,
                'activado' => $activar,
                'message' => "{$emoji} ¡Listo! {$modulo} ha sido {$accion}.{$mensajeSeguimiento}"
            ];

        } catch (\Exception $e) {
            Log::error("❌ Error actualizarConfiguracion: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'No pude actualizar la configuración.'];
        }
    }

    /**
     * Obtiene la factura con mayor margen de ganancia
     */
    private function obtenerFacturaMasRentableDB($args)
    {
        try {
            $periodo = $args['periodo'] ?? 'mes';
            $limite = intval($args['limite'] ?? 5);

            // Determinar rango de fechas
            $fechaInicio = match($periodo) {
                'hoy' => Carbon::today(),
                'ayer' => Carbon::yesterday(),
                'semana' => Carbon::now()->startOfWeek(),
                'mes' => Carbon::now()->startOfMonth(),
                'año' => Carbon::now()->startOfYear(),
                default => Carbon::now()->startOfMonth()
            };

            // Buscar facturas con sus items y calcular margen
            $facturas = Invoice::where('type', 'invoice')
                ->where('status', '!=', 'cancelled')
                ->where('date', '>=', $fechaInicio)
                ->with(['items.product', 'customer'])
                ->get();

            if ($facturas->isEmpty()) {
                return [
                    'status' => 'empty',
                    'message' => "No encontré facturas en el período '{$periodo}'."
                ];
            }

            // Calcular margen por factura
            $facturasConMargen = $facturas->map(function($factura) {
                $totalVenta = $factura->total;
                $totalCosto = 0;

                foreach ($factura->items as $item) {
                    $costoProd = $item->product->cost_price ?? 0;
                    $totalCosto += $costoProd * $item->quantity;
                }

                $margen = $totalVenta - $totalCosto;
                $porcentajeMargen = $totalVenta > 0 ? ($margen / $totalVenta) * 100 : 0;

                return [
                    'id' => $factura->id,
                    'numero' => $factura->invoice_number,
                    'fecha' => Carbon::parse($factura->date)->format('d/m/Y'),
                    'cliente' => $factura->customer->name ?? 'Cliente general',
                    'total_venta' => $totalVenta,
                    'total_costo' => $totalCosto,
                    'margen' => $margen,
                    'porcentaje_margen' => round($porcentajeMargen, 1),
                    'items_count' => $factura->items->count()
                ];
            })->sortByDesc('margen')->take($limite)->values();

            $mejor = $facturasConMargen->first();

            $mensaje = "🏆 **Facturas más rentables ({$periodo}):**\n\n";
            foreach ($facturasConMargen as $i => $f) {
                $mensaje .= ($i + 1) . ". **{$f['numero']}** ({$f['fecha']})\n";
                $mensaje .= "   Cliente: {$f['cliente']}\n";
                $mensaje .= "   Venta: \$" . number_format($f['total_venta'], 0, ',', '.') . "\n";
                $mensaje .= "   Margen: \$" . number_format($f['margen'], 0, ',', '.') . " ({$f['porcentaje_margen']}%)\n\n";
            }

            return [
                'status' => 'success',
                'periodo' => $periodo,
                'facturas' => $facturasConMargen->toArray(),
                'mejor_factura' => $mejor,
                'message' => $mensaje
            ];

        } catch (\Exception $e) {
            Log::error("❌ Error obtenerFacturaMasRentable: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'No pude calcular las facturas más rentables.'];
        }
    }

    /**
     * Analiza el negocio y da recomendaciones inteligentes
     */
    private function analizarNegocioDB()
    {
        try {
            $recomendaciones = [];

            // 1. Productos sin stock
            $sinStock = Product::where('active', true)
                ->where('current_stock', '<=', 0)
                ->count();
            if ($sinStock > 0) {
                $recomendaciones[] = [
                    'tipo' => 'stock',
                    'prioridad' => 'alta',
                    'mensaje' => "⚠️ Tienes {$sinStock} productos sin stock. Esto significa ventas perdidas."
                ];
            }

            // 2. Productos con stock bajo
            $stockBajo = Product::where('active', true)
                ->whereColumn('current_stock', '<=', 'min_stock')
                ->where('current_stock', '>', 0)
                ->count();
            if ($stockBajo > 0) {
                $recomendaciones[] = [
                    'tipo' => 'stock',
                    'prioridad' => 'media',
                    'mensaje' => "📦 {$stockBajo} productos están por agotarse. Considera hacer pedidos pronto."
                ];
            }

            // 3. Productos sin ventas en 30 días
            $hace30Dias = Carbon::now()->subDays(30);
            $productosConVentas = InvoiceItem::where('created_at', '>=', $hace30Dias)
                ->distinct()
                ->pluck('product_id');
            $sinVentas = Product::where('active', true)
                ->whereNotIn('id', $productosConVentas)
                ->count();
            if ($sinVentas > 5) {
                $recomendaciones[] = [
                    'tipo' => 'ventas',
                    'prioridad' => 'media',
                    'mensaje' => "📉 {$sinVentas} productos no se han vendido en 30 días. Considera promociones o ajustar precios."
                ];
            }

            // 4. Margen promedio
            $productosConMargen = Product::where('active', true)
                ->where('cost_price', '>', 0)
                ->where('sale_price', '>', 0)
                ->get();
            if ($productosConMargen->count() > 0) {
                $margenPromedio = $productosConMargen->avg(function($p) {
                    return (($p->sale_price - $p->cost_price) / $p->sale_price) * 100;
                });
                if ($margenPromedio < 20) {
                    $recomendaciones[] = [
                        'tipo' => 'rentabilidad',
                        'prioridad' => 'alta',
                        'mensaje' => "💰 Tu margen promedio es " . round($margenPromedio, 1) . "%. Es bajo. Revisa tus precios de venta."
                    ];
                } elseif ($margenPromedio > 40) {
                    $recomendaciones[] = [
                        'tipo' => 'positivo',
                        'prioridad' => 'info',
                        'mensaje' => "✅ Excelente margen promedio de " . round($margenPromedio, 1) . "%."
                    ];
                }
            }

            // 5. Ventas de hoy vs ayer
            $ventasHoy = Invoice::where('type', 'invoice')
                ->where('status', '!=', 'cancelled')
                ->whereDate('date', Carbon::today())
                ->sum('total');
            $ventasAyer = Invoice::where('type', 'invoice')
                ->where('status', '!=', 'cancelled')
                ->whereDate('date', Carbon::yesterday())
                ->sum('total');
            
            if ($ventasAyer > 0 && $ventasHoy < $ventasAyer * 0.7) {
                $recomendaciones[] = [
                    'tipo' => 'ventas',
                    'prioridad' => 'info',
                    'mensaje' => "📊 Las ventas de hoy (\$" . number_format($ventasHoy, 0, ',', '.') . 
                                ") van más bajas que ayer (\$" . number_format($ventasAyer, 0, ',', '.') . ")."
                ];
            }

            // 6. Clientes con deuda alta (si aplica)
            try {
                $clientesConDeuda = Customer::where('credit_balance', '>', 100000)->count();
                if ($clientesConDeuda > 0) {
                    $recomendaciones[] = [
                        'tipo' => 'cartera',
                        'prioridad' => 'media',
                        'mensaje' => "💳 Tienes {$clientesConDeuda} clientes con deudas mayores a \$100.000. Considera hacer seguimiento."
                    ];
                }
            } catch (\Exception $e) {}

            // Construir mensaje
            if (empty($recomendaciones)) {
                return [
                    'status' => 'success',
                    'recomendaciones' => [],
                    'message' => "🎉 ¡Tu negocio está en buen estado! No encontré problemas críticos. Sigue así."
                ];
            }

            $mensaje = "📋 **Análisis de tu negocio:**\n\n";
            foreach ($recomendaciones as $rec) {
                $mensaje .= $rec['mensaje'] . "\n\n";
            }
            $mensaje .= "¿Quieres que te ayude con alguno de estos puntos?";

            return [
                'status' => 'success',
                'recomendaciones' => $recomendaciones,
                'resumen' => [
                    'productos_sin_stock' => $sinStock,
                    'productos_stock_bajo' => $stockBajo,
                    'sin_ventas_30_dias' => $sinVentas ?? 0,
                    'ventas_hoy' => $ventasHoy,
                    'ventas_ayer' => $ventasAyer
                ],
                'message' => $mensaje
            ];

        } catch (\Exception $e) {
            Log::error("❌ Error analizarNegocio: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'No pude analizar tu negocio en este momento.'];
        }
    }

    // ========================================
    // NUEVAS FUNCIONES: PROVEEDORES Y ANÁLISIS
    // ========================================

    /**
     * Consulta proveedores con estadísticas
     */
    private function consultarProveedoresDB($args)
    {
        try {
            $query = trim($args['query'] ?? '');
            $soloActivos = $args['solo_activos'] ?? true;
            $limite = intval($args['limite'] ?? 20);

            $proveedoresQuery = Supplier::query();

            if ($soloActivos) {
                $proveedoresQuery->where('active', true);
            }

            if (!empty($query)) {
                $proveedoresQuery->where(function($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('contact_person', 'LIKE', "%{$query}%")
                      ->orWhere('email', 'LIKE', "%{$query}%");
                });
            }

            $proveedores = $proveedoresQuery
                ->orderBy('name')
                ->limit($limite)
                ->get();

            $totalProveedores = Supplier::when($soloActivos, fn($q) => $q->where('active', true))->count();

            if ($proveedores->isEmpty()) {
                return [
                    'status' => 'success',
                    'count' => 0,
                    'message' => empty($query) 
                        ? "No tienes proveedores registrados todavía."
                        : "No encontré proveedores con '{$query}'."
                ];
            }

            // Formatear resultados
            $lista = $proveedores->map(function($p) {
                $productos = Product::where('supplier_id', $p->id)->count();
                return [
                    'id' => $p->id,
                    'nombre' => $p->name,
                    'contacto' => $p->contact_person ?? 'Sin contacto',
                    'telefono' => $p->phone ?? 'Sin teléfono',
                    'email' => $p->email ?? 'Sin email',
                    'productos_asociados' => $productos,
                    'deuda_pendiente' => '$' . number_format($p->current_debt ?? 0, 0, ',', '.'),
                    'total_comprado' => '$' . number_format($p->total_purchased ?? 0, 0, ',', '.'),
                    'ultimo_pedido' => $p->last_order_date ? Carbon::parse($p->last_order_date)->format('d/m/Y') : 'Nunca',
                    'activo' => $p->active
                ];
            })->toArray();

            $deudaTotal = $proveedores->sum('current_debt');
            $totalComprado = $proveedores->sum('total_purchased');

            $mensaje = "🏭 Tienes **{$totalProveedores} proveedores** registrados.\n\n";
            
            foreach ($lista as $p) {
                $mensaje .= "• **{$p['nombre']}**\n";
                $mensaje .= "  📦 {$p['productos_asociados']} productos | 💰 Comprado: {$p['total_comprado']}\n";
                if (floatval(str_replace(['$', '.'], '', $p['deuda_pendiente'])) > 0) {
                    $mensaje .= "  ⚠️ Le debemos: {$p['deuda_pendiente']}\n";
                }
                $mensaje .= "\n";
            }

            if ($deudaTotal > 0) {
                $mensaje .= "💳 **Deuda total a proveedores:** \$" . number_format($deudaTotal, 0, ',', '.');
            }

            return [
                'status' => 'success',
                'count' => $totalProveedores,
                'proveedores' => $lista,
                'deuda_total' => $deudaTotal,
                'total_comprado' => $totalComprado,
                'message' => $mensaje
            ];

        } catch (\Exception $e) {
            Log::error("❌ Error consultarProveedores: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'No pude consultar los proveedores.'];
        }
    }

    /**
     * Obtiene el producto más vendido de TODO el histórico
     * NO pide período - usa toda la base de datos por defecto
     */
    private function productoMasVendidoDB($args)
    {
        try {
            $limite = intval($args['limite'] ?? 10);
            $periodo = $args['periodo'] ?? 'todo'; // Por defecto TODO el histórico

            // Base query - productos más vendidos por cantidad
            $query = InvoiceItem::select(
                    'product_id',
                    'product_name',
                    DB::raw('SUM(quantity) as total_vendido'),
                    DB::raw('SUM(subtotal) as total_facturado'),
                    DB::raw('COUNT(DISTINCT invoice_id) as num_ventas')
                )
                ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
                ->where('invoices.type', 'invoice')
                ->where('invoices.status', '!=', 'cancelled')
                ->groupBy('product_id', 'product_name');

            // Solo aplicar filtro de fecha si el usuario especificó un período diferente a "todo"
            if ($periodo !== 'todo') {
                $fechaInicio = match($periodo) {
                    'hoy' => Carbon::today(),
                    'semana' => Carbon::now()->startOfWeek(),
                    'mes' => Carbon::now()->startOfMonth(),
                    'año' => Carbon::now()->startOfYear(),
                    default => null
                };
                
                if ($fechaInicio) {
                    $query->where('invoices.date', '>=', $fechaInicio);
                }
            }

            $productos = $query
                ->orderByDesc('total_vendido')
                ->limit($limite)
                ->get();

            if ($productos->isEmpty()) {
                return [
                    'status' => 'success',
                    'count' => 0,
                    'message' => "No encontré ventas registradas" . ($periodo !== 'todo' ? " en este período." : ".")
                ];
            }

            // El más vendido es el primero
            $topProducto = $productos->first();
            
            $mensaje = "🏆 **Producto más vendido" . ($periodo !== 'todo' ? " ({$periodo})" : " (histórico total)") . ":**\n\n";
            $mensaje .= "🥇 **{$topProducto->product_name}**\n";
            $mensaje .= "   📦 {$topProducto->total_vendido} unidades vendidas\n";
            $mensaje .= "   💰 Facturado: \$" . number_format($topProducto->total_facturado, 0, ',', '.') . "\n";
            $mensaje .= "   🧾 En {$topProducto->num_ventas} facturas diferentes\n\n";

            if ($productos->count() > 1) {
                $mensaje .= "**Top {$limite} productos más vendidos:**\n";
                $posicion = 1;
                foreach ($productos as $p) {
                    $medalla = match($posicion) {
                        1 => '🥇',
                        2 => '🥈',
                        3 => '🥉',
                        default => "{$posicion}."
                    };
                    $mensaje .= "{$medalla} {$p->product_name} - {$p->total_vendido} uds (\$" . number_format($p->total_facturado, 0, ',', '.') . ")\n";
                    $posicion++;
                }
            }

            return [
                'status' => 'success',
                'periodo' => $periodo,
                'top_producto' => [
                    'nombre' => $topProducto->product_name,
                    'unidades_vendidas' => $topProducto->total_vendido,
                    'total_facturado' => $topProducto->total_facturado,
                    'num_facturas' => $topProducto->num_ventas
                ],
                'ranking' => $productos->map(fn($p) => [
                    'nombre' => $p->product_name,
                    'vendido' => $p->total_vendido,
                    'facturado' => $p->total_facturado
                ])->toArray(),
                'message' => $mensaje
            ];

        } catch (\Exception $e) {
            Log::error("❌ Error productoMasVendido: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'No pude consultar los productos más vendidos.'];
        }
    }

    /**
     * Productos con poca o ninguna venta + RECOMENDACIONES ACCIONABLES
     */
    private function productosPocoVendidosDB($args)
    {
        try {
            $limite = intval($args['limite'] ?? 10);
            $diasSinVenta = intval($args['dias_sin_venta'] ?? 30);

            $fechaCorte = Carbon::now()->subDays($diasSinVenta);

            // Productos que SÍ se vendieron en el período
            $productosConVentas = InvoiceItem::join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
                ->where('invoices.date', '>=', $fechaCorte)
                ->where('invoices.type', 'invoice')
                ->where('invoices.status', '!=', 'cancelled')
                ->distinct()
                ->pluck('product_id');

            // Productos activos que NO se vendieron
            $productosSinVenta = Product::where('active', true)
                ->where('current_stock', '>', 0) // Solo los que tienen stock
                ->whereNotIn('id', $productosConVentas)
                ->orderByDesc(DB::raw('sale_price * current_stock')) // Mayor valor estancado primero
                ->limit($limite)
                ->get();

            if ($productosSinVenta->isEmpty()) {
                return [
                    'status' => 'success',
                    'count' => 0,
                    'message' => "🎉 ¡Excelente! Todos tus productos con stock se han vendido en los últimos {$diasSinVenta} días."
                ];
            }

            $totalEstancado = $productosSinVenta->sum(fn($p) => $p->sale_price * $p->current_stock);

            $mensaje = "📉 **Productos sin ventas en {$diasSinVenta} días:**\n\n";
            $mensaje .= "💰 Valor total estancado: \$" . number_format($totalEstancado, 0, ',', '.') . "\n\n";

            $recomendaciones = [];
            foreach ($productosSinVenta as $p) {
                $valorEstancado = $p->sale_price * $p->current_stock;
                $mensaje .= "• **{$p->name}**\n";
                $mensaje .= "  Stock: {$p->current_stock} | Precio: \$" . number_format($p->sale_price, 0, ',', '.') . "\n";
                $mensaje .= "  💵 Valor estancado: \$" . number_format($valorEstancado, 0, ',', '.') . "\n\n";

                $recomendaciones[] = [
                    'producto_id' => $p->id,
                    'nombre' => $p->name,
                    'stock' => $p->current_stock,
                    'precio_actual' => $p->sale_price,
                    'valor_estancado' => $valorEstancado,
                    'acciones_sugeridas' => [
                        '📉 Bajar precio un 10-20% para impulsar ventas',
                        '🏷️ Crear combo o promoción con productos populares',
                        '📱 Enviar oferta por WhatsApp a clientes registrados',
                        '📦 Considerar devolución a proveedor si es posible'
                    ]
                ];
            }

            $mensaje .= "---\n**🎯 RECOMENDACIONES:**\n\n";
            $mensaje .= "1. **Baja los precios** un 10-20% para estos productos\n";
            $mensaje .= "2. **Crea combos** mezclándolos con productos populares\n";
            $mensaje .= "3. **Envía promociones** por WhatsApp a tus clientes\n";
            $mensaje .= "4. **Ubícalos visiblemente** en tu tienda física\n\n";
            $mensaje .= "¿Quieres que te ayude a bajar el precio de alguno o crear una promoción?";

            return [
                'status' => 'success',
                'count' => $productosSinVenta->count(),
                'dias_analizados' => $diasSinVenta,
                'valor_total_estancado' => $totalEstancado,
                'productos' => $recomendaciones,
                'message' => $mensaje
            ];

        } catch (\Exception $e) {
            Log::error("❌ Error productosPocoVendidos: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'No pude analizar los productos sin ventas.'];
        }
    }

    // ========================================
    // NAVEGACIÓN Y CONTROL DEL SISTEMA
    // ========================================

    /**
     * Navegar a un módulo específico del sistema
     * Devuelve una acción que el frontend interpretará
     */
    private function navegarModuloAction($args)
    {
        $modulo = strtolower(trim($args['modulo'] ?? ''));
        $mensaje = $args['mensaje'] ?? '';

        // Mapeo de módulos a nombres REALES del frontend (según Sidebar.vue)
        // IMPORTANTE: Estos son los IDs exactos que usa el sistema
        $moduloMap = [
            // Dashboard
            'dashboard' => 'dashboard',
            'inicio' => 'dashboard',
            'home' => 'dashboard',
            
            // Operaciones
            'pos' => 'pos',
            'punto de venta' => 'pos',
            'venta' => 'pos',
            'ventas' => 'pos',
            'vender' => 'pos',
            
            'facturas' => 'invoices',
            'facturacion' => 'invoices',
            'factura' => 'invoices',
            'invoices' => 'invoices',
            
            'devoluciones' => 'returns-management',
            'devolucion' => 'returns-management',
            'returns' => 'returns-management',
            
            // Inventario
            'productos' => 'products',
            'producto' => 'products',
            'inventario' => 'products',
            'products' => 'products',
            
            'categorias' => 'categories',
            'categoria' => 'categories',
            'categories' => 'categories',
            
            'stock' => 'stock',
            'gestion de stock' => 'stock',
            
            'inventario inteligente' => 'intelligent_inventory',
            'inventario ia' => 'intelligent_inventory',
            
            // Tienda Online
            'catalogo web' => 'web-catalog-config',
            'tienda online' => 'web-catalog-config',
            'catalogo' => 'web-catalog-config',
            
            // Multisede
            'sedes' => 'warehouses',
            'bodegas' => 'warehouses',
            'sucursales' => 'warehouses',
            
            // Relaciones
            'clientes' => 'customers',
            'cliente' => 'customers',
            'customers' => 'customers',
            
            'creditienda' => 'accounts-receivable',
            'creditos' => 'accounts-receivable',
            'cuentas por cobrar' => 'accounts-receivable',
            
            'proveedores' => 'purchase-orders',
            'proveedor' => 'purchase-orders',
            'ordenes de compra' => 'purchase-orders',
            'compras' => 'purchase-orders',
            
            // Sistema
            'usuarios' => 'users',
            'usuario' => 'users',
            'users' => 'users',
            
            'caja' => 'cash-admin',
            'cajas' => 'cash-admin',
            'control de cajas' => 'cash-admin',
            
            'gastos' => 'expenses',
            'gastos operativos' => 'expenses',
            'egresos' => 'expenses',
            
            'reportes' => 'reports',
            'reporte' => 'reports',
            'informes' => 'reports',
            'reports' => 'reports',
            
            'configuracion' => 'settings',
            'config' => 'settings',
            'ajustes' => 'settings',
            'settings' => 'settings'
        ];

        if (!isset($moduloMap[$modulo])) {
            return [
                'status' => 'error',
                'message' => "No reconozco el módulo '{$modulo}'. Los módulos disponibles son: dashboard, pos/ventas, facturas, productos, categorías, stock, clientes, proveedores, reportes, configuración, cajas, gastos, devoluciones."
            ];
        }

        $moduloDestino = $moduloMap[$modulo];

        // Nombres legibles para el mensaje
        $nombresLegibles = [
            'dashboard' => 'Dashboard',
            'pos' => 'Punto de Venta',
            'invoices' => 'Facturas',
            'returns-management' => 'Devoluciones',
            'products' => 'Productos',
            'categories' => 'Categorías',
            'stock' => 'Gestión de Stock',
            'intelligent_inventory' => 'Inventario Inteligente',
            'web-catalog-config' => 'Catálogo Web',
            'warehouses' => 'Gestión de Sedes',
            'customers' => 'Clientes',
            'accounts-receivable' => 'CrediTienda',
            'purchase-orders' => 'Proveedores',
            'users' => 'Usuarios',
            'cash-admin' => 'Control de Cajas',
            'expenses' => 'Gastos Operativos',
            'reports' => 'Reportes',
            'settings' => 'Configuración'
        ];

        $nombreLegible = $nombresLegibles[$moduloDestino] ?? ucfirst($moduloDestino);

        return [
            'status' => 'success',
            'action' => [
                'type' => 'navigate',
                'payload' => [
                    'params' => ['module' => $moduloDestino]
                ]
            ],
            'message' => $mensaje ?: "🚀 Te llevo al módulo de **{$nombreLegible}**..."
        ];
    }

    /**
     * Controlar la radio del sistema
     * Devuelve una acción que el frontend interpretará
     */
    // ========================================
    // FUNCIONES DE BASE DE DATOS - DEVOLUCIONES
    // ========================================

    private function consultarDevolucionesDB($args)
    {
        try {
            $periodo = $args['periodo'] ?? null;
            $fecha = $args['fecha'] ?? null;
            $estado = $args['estado'] ?? null;
            $limite = intval($args['limite'] ?? 10);

            $query = ProductReturn::with(['customer:id,name', 'originalInvoice:id,number', 'user:id,name']);

            $periodoTexto = 'todos los tiempos';

            // Filtrar por fecha específica
            if ($fecha) {
                try {
                    $fechaCarbon = Carbon::parse($fecha);
                    $query->whereDate('return_date', $fechaCarbon);
                    $periodoTexto = $fechaCarbon->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY');
                } catch (\Exception $e) {
                    return ['status' => 'error', 'message' => "La fecha '{$fecha}' no es válida. Usa formato YYYY-MM-DD."];
                }
            }
            // Filtrar por período
            elseif ($periodo) {
                switch ($periodo) {
                    case 'hoy':
                        $query->whereDate('return_date', Carbon::today());
                        $periodoTexto = 'hoy (' . Carbon::today()->format('d/m/Y') . ')';
                        break;
                    case 'ayer':
                        $query->whereDate('return_date', Carbon::yesterday());
                        $periodoTexto = 'ayer (' . Carbon::yesterday()->format('d/m/Y') . ')';
                        break;
                    case 'semana':
                        $query->where('return_date', '>=', Carbon::now()->startOfWeek());
                        $periodoTexto = 'esta semana';
                        break;
                    case 'mes':
                        $query->where('return_date', '>=', Carbon::now()->startOfMonth());
                        $periodoTexto = 'este mes (' . Carbon::now()->locale('es')->isoFormat('MMMM YYYY') . ')';
                        break;
                    case 'año':
                        $query->where('return_date', '>=', Carbon::now()->startOfYear());
                        $periodoTexto = 'este año (' . Carbon::now()->format('Y') . ')';
                        break;
                    case 'ultimos_7_dias':
                        $query->where('return_date', '>=', Carbon::now()->subDays(7));
                        $periodoTexto = 'los últimos 7 días';
                        break;
                    case 'ultimos_30_dias':
                        $query->where('return_date', '>=', Carbon::now()->subDays(30));
                        $periodoTexto = 'los últimos 30 días';
                        break;
                }
            }

            // Filtrar por estado
            if ($estado) {
                $query->where('status', $estado);
            }

            $totalDevoluciones = (clone $query)->sum('total');
            $cantidadDevoluciones = (clone $query)->count();

            $devoluciones = $query->orderBy('return_date', 'desc')
                ->limit($limite)
                ->get()
                ->map(function($d) {
                    $estadoTexto = match($d->status) {
                        'completed' => '✅ Completada',
                        'pending' => '⏳ Pendiente',
                        'cancelled' => '❌ Cancelada',
                        default => $d->status
                    };

                    $metodoTexto = match($d->refund_method) {
                        'cash' => 'Efectivo',
                        'credit' => 'Nota crédito',
                        'transfer' => 'Transferencia',
                        default => $d->refund_method ?? 'No especificado'
                    };

                    return [
                        'id' => $d->id,
                        'numero' => $d->number,
                        'cliente' => $d->customer?->name ?? 'Cliente General',
                        'factura_original' => $d->originalInvoice?->number ?? 'N/A',
                        'fecha' => Carbon::parse($d->return_date)->format('d/m/Y'),
                        'total' => '$' . number_format($d->total, 0, ',', '.'),
                        'total_raw' => $d->total,
                        'estado' => $estadoTexto,
                        'razon' => $d->reason ?? 'Sin razón especificada',
                        'metodo_reembolso' => $metodoTexto,
                        'realizada_por' => $d->user?->name ?? 'Sistema'
                    ];
                });

            $totalFmt = '$' . number_format($totalDevoluciones, 0, ',', '.');

            if ($cantidadDevoluciones === 0) {
                return [
                    'periodo' => $periodoTexto,
                    'total_devoluciones' => 0,
                    'total_formateado' => '$0',
                    'cantidad' => 0,
                    'devoluciones' => [],
                    'hay_devoluciones' => false,
                    'mensaje' => "📊 No encontré devoluciones para {$periodoTexto}."
                ];
            }

            $listaDevoluciones = $devoluciones->take(5)->map(function($d) {
                return "• {$d['numero']}: Cliente: {$d['cliente']} | Total: {$d['total']} | Estado: {$d['estado']} | Razón: {$d['razon']} | Factura original: {$d['factura_original']} | Reembolso: {$d['metodo_reembolso']} | Realizada por: {$d['realizada_por']} | Fecha: {$d['fecha']}";
            })->join("\n");

            $mensajeMas = $cantidadDevoluciones > 5 ? "\n...y " . ($cantidadDevoluciones - 5) . " devoluciones más." : "";

            return [
                'periodo' => $periodoTexto,
                'total_devoluciones' => $totalDevoluciones,
                'total_formateado' => $totalFmt,
                'cantidad' => $cantidadDevoluciones,
                'devoluciones' => $devoluciones->toArray(),
                'hay_devoluciones' => true,
                'mensaje' => "🔄 **Devoluciones de {$periodoTexto}**\n\n" .
                            "• Total devuelto: {$totalFmt}\n" .
                            "• Cantidad: {$cantidadDevoluciones}\n\n" .
                            "📋 **Detalle:**\n{$listaDevoluciones}{$mensajeMas}"
            ];

        } catch (\Exception $e) {
            Log::error("Error consultarDevoluciones: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Tuve un problema al consultar las devoluciones. Por favor intenta de nuevo.'];
        }
    }

    private function controlarRadioAction($args)
    {
        $accion = strtolower(trim($args['accion'] ?? ''));
        $volumen = isset($args['volumen']) ? intval($args['volumen']) : null;

        $accionesValidas = ['play', 'pause', 'toggle', 'next', 'previous', 'volume_up', 'volume_down', 'mute'];

        if (!in_array($accion, $accionesValidas)) {
            return [
                'status' => 'error',
                'message' => "No reconozco la acción '{$accion}'. Puedo: reproducir, pausar, siguiente, anterior, subir/bajar volumen."
            ];
        }

        $mensajes = [
            'play' => '🎵 ¡Listo! La radio está sonando.',
            'pause' => '⏸️ Radio pausada.',
            'toggle' => '🎵 Cambiando estado de la radio...',
            'next' => '⏭️ Siguiente estación...',
            'previous' => '⏮️ Estación anterior...',
            'volume_up' => '🔊 Subiendo volumen...',
            'volume_down' => '🔉 Bajando volumen...',
            'mute' => '🔇 Radio silenciada.'
        ];

        $payload = ['action' => $accion];
        if ($volumen !== null) {
            $payload['volume'] = max(0, min(100, $volumen));
        }

        return [
            'status' => 'success',
            'action' => [
                'type' => 'radio',
                'payload' => $payload
            ],
            'message' => $mensajes[$accion] ?? '🎵 Controlando radio...'
        ];
    }

    /**
     * 🛡️ Sanitiza respuestas de la IA para evitar mostrar código técnico al usuario
     * A veces Gemini "alucina" y genera código Python/JS en lugar de ejecutar funciones
     */
    private function sanitizeAIResponse(string $response, string $originalPrompt): string
    {
        // Patrones de código técnico que NO deben mostrarse al usuario
        $technicalPatterns = [
            '/print\s*\(\s*\w+\.\w+\(/i',           // print(default_api.xxx(
            '/default_api\./i',                      // default_api.
            '/api\.\w+\(/i',                         // api.xxx(
            '/import\s+\w+/i',                       // import xxx
            '/from\s+\w+\s+import/i',                // from xxx import
            '/def\s+\w+\s*\(/i',                     // def xxx(
            '/function\s+\w+\s*\(/i',                // function xxx(
            '/console\.log\(/i',                     // console.log(
            '/await\s+\w+\.\w+\(/i',                 // await xxx.xxx(
            '/```python/i',                          // código Python
            '/```javascript/i',                      // código JavaScript
            '/controlarRadio\s*\(/i',                // controlarRadio(
            '/navegarModulo\s*\(/i',                 // navegarModulo(
        ];

        foreach ($technicalPatterns as $pattern) {
            if (preg_match($pattern, $response)) {
                Log::warning('🛡️ [Gemini] Respuesta con código técnico detectada y filtrada', [
                    'original_prompt' => substr($originalPrompt, 0, 100),
                    'bad_response' => substr($response, 0, 200)
                ]);

                // Detectar qué quería hacer el usuario y dar respuesta amigable
                $promptLower = strtolower($originalPrompt);
                
                // Radio/Música
                if (preg_match('/radio|música|musica|pon|prende|reproduce|play/i', $promptLower)) {
                    return '🎵 ¡Listo! Poniendo música para ti.';
                }
                
                // Pausar
                if (preg_match('/para|pausa|stop|detén|deten/i', $promptLower)) {
                    return '⏸️ Música pausada.';
                }
                
                // Siguiente
                if (preg_match('/siguiente|next|otra|cambia/i', $promptLower)) {
                    return '⏭️ Siguiente canción...';
                }
                
                // Navegación
                if (preg_match('/llévame|llevame|ir a|ve a|abre|muéstrame|muestrame/i', $promptLower)) {
                    return '🚀 Te llevo ahí...';
                }

                // Respuesta genérica
                return '¡Entendido! Estoy procesando tu solicitud.';
            }
        }

        return $response;
    }
}