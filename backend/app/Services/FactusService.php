<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Servicio de integración con Factus API
 * Para facturación electrónica DIAN Colombia
 * 
 * NOTA: Las credenciales son GLOBALES (desde .env)
 * 105POS compra paquete de facturas a Factus y las distribuye entre tenants
 * Los tenants NO configuran credenciales - solo usan el servicio
 * 
 * Documentación: https://developers.factus.com.co/
 */
class FactusService
{
    private string $baseUrl;
    private string $clientId;
    private string $clientSecret;
    private string $username;
    private string $password;
    private bool $isSandbox;
    private bool $isEnabled;
    private ?string $numberingRangeId;
    
    // URLs de la API
    const SANDBOX_URL = 'https://api-sandbox.factus.com.co';
    const PRODUCTION_URL = 'https://api.factus.com.co';
    
    // Tipos de documento de identidad
    const DOC_TYPE_REGISTRO_CIVIL = 1;
    const DOC_TYPE_TARJETA_IDENTIDAD = 2;
    const DOC_TYPE_CEDULA_CIUDADANIA = 3;
    const DOC_TYPE_TARJETA_EXTRANJERIA = 4;
    const DOC_TYPE_CEDULA_EXTRANJERIA = 5;
    const DOC_TYPE_NIT = 6;
    const DOC_TYPE_PASAPORTE = 7;
    
    // Métodos de pago DIAN
    const PAYMENT_CASH = '10';           // Efectivo
    const PAYMENT_CONSIGNACION = '42';   // Consignación
    const PAYMENT_TRANSFER = '47';       // Transferencia
    const PAYMENT_CREDIT_CARD = '48';    // Tarjeta de Crédito
    const PAYMENT_DEBIT_CARD = '49';     // Tarjeta Débito
    const PAYMENT_OTHER = 'ZZZ';         // Otro
    
    // Formas de pago
    const PAYMENT_FORM_CASH = '1';       // Contado
    const PAYMENT_FORM_CREDIT = '2';     // Crédito
    
    public function __construct(array $config = [])
    {
        $this->isEnabled = $config['enabled'] ?? false;
        $this->isSandbox = $config['sandbox'] ?? true;
        $this->baseUrl = $this->isSandbox ? self::SANDBOX_URL : self::PRODUCTION_URL;
        $this->clientId = $config['client_id'] ?? '';
        $this->clientSecret = $config['client_secret'] ?? '';
        $this->username = $config['username'] ?? '';
        $this->password = $config['password'] ?? '';
        $this->numberingRangeId = $config['numbering_range_id'] ?? null;
    }
    
    /**
     * Crear instancia con credenciales GLOBALES desde .env
     * Los tenants NO configuran credenciales - 105POS las gestiona centralmente
     */
    public static function create(): self
    {
        return new self([
            'enabled' => env('FACTUS_ENABLED', false),
            'sandbox' => env('FACTUS_SANDBOX', true),
            'client_id' => env('FACTUS_CLIENT_ID', ''),
            'client_secret' => env('FACTUS_CLIENT_SECRET', ''),
            'username' => env('FACTUS_USERNAME', ''),
            'password' => env('FACTUS_PASSWORD', ''),
            'numbering_range_id' => env('FACTUS_NUMBERING_RANGE_ID', null)
        ]);
    }
    
    /**
     * @deprecated Usar create() en su lugar
     * Mantener para compatibilidad temporal
     */
    public static function forTenant(): self
    {
        return self::create();
    }
    
    /**
     * Verificar si Factus está habilitado globalmente
     */
    public function isEnabled(): bool
    {
        return $this->isEnabled && $this->isConfigured();
    }
    
    /**
     * Verificar si Factus está configurado
     */
    public function isConfigured(): bool
    {
        return !empty($this->clientId) && 
               !empty($this->clientSecret) && 
               !empty($this->username) && 
               !empty($this->password);
    }
    
    /**
     * Obtener ID del rango de numeración
     */
    public function getNumberingRangeId(): ?string
    {
        return $this->numberingRangeId;
    }
    /**
     * Obtener token de acceso OAuth2
     */
    public function getAccessToken(): string
    {
        $cacheKey = 'factus_token_' . tenant('id');
        
        // Intentar obtener token del cache
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        $response = Http::asForm()
            ->acceptJson()
            ->post("{$this->baseUrl}/oauth/token", [
                'grant_type' => 'password',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'username' => $this->username,
                'password' => $this->password
            ]);
        
        if (!$response->successful()) {
            Log::error('❌ Factus: Error de autenticación', [
                'status' => $response->status(),
                'response' => $response->json()
            ]);
            throw new \Exception('Error de autenticación con Factus: ' . ($response->json()['error_description'] ?? 'Error desconocido'));
        }
        
        $data = $response->json();
        $token = $data['access_token'];
        $expiresIn = $data['expires_in'] - 60; // Restar 60 segundos de margen
        
        // Guardar token en cache
        Cache::put($cacheKey, $token, $expiresIn);
        
        // También guardar el refresh_token
        if (isset($data['refresh_token'])) {
            Cache::put($cacheKey . '_refresh', $data['refresh_token'], 86400); // 24 horas
        }
        
        Log::info('✅ Factus: Token obtenido correctamente', [
            'expires_in' => $expiresIn
        ]);
        
        return $token;
    }
    
    /**
     * Refrescar token de acceso
     */
    public function refreshToken(): string
    {
        $cacheKey = 'factus_token_' . tenant('id');
        $refreshToken = Cache::get($cacheKey . '_refresh');
        
        if (!$refreshToken) {
            // Si no hay refresh_token, obtener uno nuevo
            return $this->getAccessToken();
        }
        
        $response = Http::asForm()
            ->acceptJson()
            ->post("{$this->baseUrl}/oauth/token", [
                'grant_type' => 'refresh_token',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'refresh_token' => $refreshToken
            ]);
        
        if (!$response->successful()) {
            // Si falla el refresh, intentar autenticación completa
            Cache::forget($cacheKey . '_refresh');
            return $this->getAccessToken();
        }
        
        $data = $response->json();
        $token = $data['access_token'];
        $expiresIn = $data['expires_in'] - 60;
        
        Cache::put($cacheKey, $token, $expiresIn);
        
        if (isset($data['refresh_token'])) {
            Cache::put($cacheKey . '_refresh', $data['refresh_token'], 86400);
        }
        
        return $token;
    }
    
    /**
     * Hacer petición autenticada a la API
     */
    private function apiRequest(string $method, string $endpoint, array $data = []): array
    {
        $token = $this->getAccessToken();
        
        // Timeout de 30 segundos (Factus puede demorar hasta 8-15 seg según documentación)
        $request = Http::withToken($token)
            ->timeout(30)
            ->connectTimeout(10)
            ->acceptJson()
            ->contentType('application/json');
        
        $url = "{$this->baseUrl}{$endpoint}";
        
        $response = match(strtoupper($method)) {
            'GET' => $request->get($url, $data),
            'POST' => $request->post($url, $data),
            'PUT' => $request->put($url, $data),
            'DELETE' => $request->delete($url),
            default => throw new \Exception("Método HTTP no soportado: {$method}")
        };
        
        if (!$response->successful()) {
            Log::error('❌ Factus: Error en petición', [
                'method' => $method,
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'response' => $response->json()
            ]);
            
            $error = $response->json();
            throw new \Exception(
                'Error de Factus: ' . 
                ($error['message'] ?? $error['error'] ?? 'Error desconocido') .
                (isset($error['errors']) ? ' - ' . json_encode($error['errors']) : '')
            );
        }
        
        return $response->json();
    }
    
    /**
     * Obtener rangos de numeración disponibles
     */
    public function getNumberingRanges(): array
    {
        return $this->apiRequest('GET', '/v1/numbering-ranges');
    }
    
    /**
     * Crear y validar factura electrónica ante la DIAN
     * 
     * @param array $invoiceData Datos de la factura
     * @return array Respuesta con CUFE, QR y datos de la factura validada
     */
    public function createInvoice(array $invoiceData): array
    {
        $factusData = $this->transformToFactusFormat($invoiceData);
        
        Log::info('📤 Factus: Enviando factura para validación', [
            'reference_code' => $factusData['reference_code'] ?? 'N/A'
        ]);
        
        $response = $this->apiRequest('POST', '/v1/bills/validate', $factusData);
        
        Log::info('✅ Factus: Factura validada', [
            'number' => $response['data']['bill']['number'] ?? 'N/A',
            'cufe' => $response['data']['bill']['cufe'] ?? 'N/A'
        ]);
        
        return $response;
    }
    
    /**
     * Transformar datos del sistema POS al formato de Factus
     */
    private function transformToFactusFormat(array $data): array
    {
        // Obtener configuración del sistema
        $settings = \DB::table('system_settings')->first();
        
        // Mapear método de pago del sistema al código DIAN
        $paymentMethodCode = $this->mapPaymentMethod($data['payment_method'] ?? 'efectivo');
        
        // Determinar forma de pago (contado/crédito)
        $paymentForm = ($data['payment_method'] ?? '') === 'credito' ? self::PAYMENT_FORM_CREDIT : self::PAYMENT_FORM_CASH;
        
        // Preparar items
        $items = [];
        foreach (($data['items'] ?? []) as $item) {
            $items[] = $this->transformItem($item, $settings);
        }
        
        // Construir factura en formato Factus
        $factusInvoice = [
            'document' => '01', // Factura electrónica de venta
            'reference_code' => $data['reference_code'] ?? $data['number'] ?? uniqid('FACT-'),
            'observation' => substr($data['notes'] ?? 'Factura generada desde 105 POS', 0, 250),
            'payment_form' => $paymentForm,
            'payment_method_code' => $paymentMethodCode,
            'operation_type' => 10, // Estándar
            'send_email' => $data['send_email'] ?? false,
            
            // Rango de numeración (opcional si solo hay uno activo)
            // 'numbering_range_id' => $settings->factus_numbering_range_id ?? null,
            
            // Cliente
            'customer' => $this->transformCustomer($data['customer'] ?? []),
            
            // Items
            'items' => $items
        ];
        
        // Si es crédito, agregar fecha de vencimiento
        if ($paymentForm === self::PAYMENT_FORM_CREDIT) {
            $dueDate = $data['due_date'] ?? date('Y-m-d', strtotime('+30 days'));
            $factusInvoice['payment_due_date'] = $dueDate;
        }
        
        // Agregar establecimiento si está configurado
        if (!empty($settings->factus_establishment_name)) {
            $factusInvoice['establishment'] = [
                'name' => $settings->factus_establishment_name,
                'address' => $settings->company_address ?? '',
                'phone_number' => $settings->company_phone ?? '',
                'email' => $settings->company_email ?? '',
                'municipality_id' => $settings->factus_municipality_id ?? '11001' // Bogotá por defecto
            ];
        }
        
        // Agregar cargos/descargos si hay descuento general
        if (!empty($data['discount_amount']) && $data['discount_amount'] > 0) {
            $factusInvoice['allowance_charges'] = [
                [
                    'concept_type' => '00', // Descuento no especificado
                    'is_surcharge' => false,
                    'reason' => 'Descuento comercial',
                    'base_amount' => (string) ($data['subtotal'] ?? 0),
                    'amount' => (string) $data['discount_amount']
                ]
            ];
        }
        
        return $factusInvoice;
    }
    
    /**
     * Transformar item al formato de Factus
     */
    private function transformItem(array $item, $settings): array
    {
        $quantity = (int) ($item['quantity'] ?? 1);
        $unitPrice = (float) ($item['unit_price'] ?? $item['price'] ?? 0);
        $discountRate = 0;
        
        // Calcular porcentaje de descuento si hay descuento
        if (!empty($item['discount_amount']) && $item['discount_amount'] > 0) {
            $originalTotal = $unitPrice * $quantity;
            if ($originalTotal > 0) {
                $discountRate = round(($item['discount_amount'] / $originalTotal) * 100, 2);
            }
        }
        
        // Determinar tasa de IVA
        $taxRate = '0.00';
        $isExcluded = 1; // Por defecto excluido
        
        if (($settings->iva_enabled ?? false) && ($settings->iva_percentage ?? 0) > 0) {
            $taxRate = number_format($settings->iva_percentage, 2, '.', '');
            $isExcluded = 0;
        }
        
        return [
            'code_reference' => $item['product_sku'] ?? $item['sku'] ?? (string)($item['product_id'] ?? 'PROD'),
            'name' => $item['product_name'] ?? $item['name'] ?? 'Producto',
            'quantity' => $quantity,
            'discount_rate' => $discountRate,
            'price' => $unitPrice, // Precio CON IVA incluido si aplica
            'tax_rate' => $taxRate,
            'unit_measure_id' => 70, // Unidad por defecto
            'standard_code_id' => 1, // Estándar del contribuyente
            'is_excluded' => $isExcluded,
            'tribute_id' => 1, // IVA
            'note' => $item['notes'] ?? ''
        ];
    }
    
    /**
     * Transformar datos del cliente al formato de Factus
     */
    private function transformCustomer(array $customer): array
    {
        // Determinar tipo de documento
        $docType = $this->mapDocumentType($customer['document_type'] ?? 'cc');
        
        // Determinar si es persona jurídica o natural
        $isCompany = $docType === self::DOC_TYPE_NIT;
        
        $factusCustomer = [
            'identification_document_id' => (string) $docType,
            'identification' => $customer['document'] ?? $customer['identification'] ?? '222222222222',
            'legal_organization_id' => $isCompany ? '1' : '2', // 1=Jurídica, 2=Natural
            'tribute_id' => '21', // No aplica tributo
            'address' => $customer['address'] ?? 'Sin dirección',
            'email' => $customer['email'] ?? 'noemail@example.com',
            'phone' => $customer['phone'] ?? ''
        ];
        
        // Si es NIT, agregar dígito de verificación y razón social
        if ($isCompany) {
            $factusCustomer['dv'] = $customer['dv'] ?? $this->calculateDV($customer['document'] ?? '');
            $factusCustomer['company'] = $customer['name'] ?? $customer['company'] ?? 'Empresa';
            $factusCustomer['trade_name'] = $customer['trade_name'] ?? $customer['name'] ?? '';
        } else {
            // Persona natural
            $factusCustomer['names'] = $customer['name'] ?? 'Consumidor Final';
        }
        
        // Municipio (opcional)
        if (!empty($customer['municipality_id'])) {
            $factusCustomer['municipality_id'] = $customer['municipality_id'];
        }
        
        return $factusCustomer;
    }
    
    /**
     * Mapear tipo de documento del sistema al ID de Factus
     */
    private function mapDocumentType(string $type): int
    {
        return match(strtolower($type)) {
            'rc', 'registro_civil' => self::DOC_TYPE_REGISTRO_CIVIL,
            'ti', 'tarjeta_identidad' => self::DOC_TYPE_TARJETA_IDENTIDAD,
            'cc', 'cedula', 'cedula_ciudadania' => self::DOC_TYPE_CEDULA_CIUDADANIA,
            'te', 'tarjeta_extranjeria' => self::DOC_TYPE_TARJETA_EXTRANJERIA,
            'ce', 'cedula_extranjeria' => self::DOC_TYPE_CEDULA_EXTRANJERIA,
            'nit' => self::DOC_TYPE_NIT,
            'pasaporte', 'passport' => self::DOC_TYPE_PASAPORTE,
            default => self::DOC_TYPE_CEDULA_CIUDADANIA
        };
    }
    
    /**
     * Mapear método de pago del sistema al código DIAN
     */
    private function mapPaymentMethod(string $method): string
    {
        return match(strtolower($method)) {
            'efectivo', 'cash' => self::PAYMENT_CASH,
            'consignacion' => self::PAYMENT_CONSIGNACION,
            'transferencia', 'transfer' => self::PAYMENT_TRANSFER,
            'tarjeta_credito', 'credit_card', 'tarjeta' => self::PAYMENT_CREDIT_CARD,
            'tarjeta_debito', 'debit_card', 'debito' => self::PAYMENT_DEBIT_CARD,
            'credito', 'creditienda' => self::PAYMENT_OTHER,
            default => self::PAYMENT_CASH
        };
    }
    
    /**
     * Calcular dígito de verificación del NIT
     */
    private function calculateDV(string $nit): string
    {
        $nit = preg_replace('/[^0-9]/', '', $nit);
        
        if (empty($nit)) {
            return '0';
        }
        
        $primes = [3, 7, 13, 17, 19, 23, 29, 37, 41, 43, 47, 53, 59, 67, 71];
        $nit = str_pad($nit, 15, '0', STR_PAD_LEFT);
        $sum = 0;
        
        for ($i = 0; $i < 15; $i++) {
            $sum += (int) $nit[$i] * $primes[$i];
        }
        
        $remainder = $sum % 11;
        
        return (string) ($remainder > 1 ? 11 - $remainder : $remainder);
    }
    
    /**
     * Descargar PDF de factura validada
     */
    public function downloadPDF(string $invoiceNumber): array
    {
        return $this->apiRequest('GET', "/v1/bills/download-pdf/{$invoiceNumber}");
    }
    
    /**
     * Descargar XML de factura validada
     */
    public function downloadXML(string $invoiceNumber): array
    {
        return $this->apiRequest('GET', "/v1/bills/download-xml/{$invoiceNumber}");
    }
    
    /**
     * Obtener detalle de una factura
     */
    public function getInvoice(string $invoiceNumber): array
    {
        return $this->apiRequest('GET', "/v1/bills/show/{$invoiceNumber}");
    }
    
    /**
     * Listar todas las facturas
     */
    public function listInvoices(array $params = []): array
    {
        return $this->apiRequest('GET', '/v1/bills', $params);
    }
    
    /**
     * Eliminar factura no validada (por reference_code)
     */
    public function deleteByReference(string $referenceCode): array
    {
        return $this->apiRequest('DELETE', "/v1/bills/destroy/reference/{$referenceCode}");
    }
    
    /**
     * Obtener eventos RADIAN de una factura
     */
    public function getRadianEvents(string $invoiceNumber): array
    {
        return $this->apiRequest('GET', "/v1/bills/{$invoiceNumber}/radian/events");
    }
    
    /**
     * Crear nota crédito
     */
    public function createCreditNote(array $data): array
    {
        // Las notas crédito tienen su propia estructura
        // Se referencia la factura original con el CUFE
        $factusData = [
            'document' => '91', // Nota crédito
            'reference_code' => $data['reference_code'] ?? uniqid('NC-'),
            'observation' => $data['reason'] ?? 'Nota crédito',
            
            // Referencia a factura original
            'billing_reference' => [
                'number' => $data['original_invoice_number'],
                'uuid' => $data['original_cufe'],
                'issue_date' => $data['original_invoice_date']
            ],
            
            // Concepto de corrección (1-4)
            'discrepancy_response' => [
                'correction_concept_id' => $data['correction_concept'] ?? 2 // 2 = Anulación de factura
            ],
            
            'customer' => $this->transformCustomer($data['customer'] ?? []),
            'items' => array_map(fn($item) => $this->transformItem($item, \DB::table('system_settings')->first()), $data['items'] ?? [])
        ];
        
        return $this->apiRequest('POST', '/v1/credit-notes/validate', $factusData);
    }
    
    /**
     * Probar conexión con la API
     */
    public function testConnection(): array
    {
        try {
            $token = $this->getAccessToken();
            $ranges = $this->getNumberingRanges();
            
            return [
                'success' => true,
                'message' => 'Conexión exitosa con Factus',
                'token_valid' => !empty($token),
                'numbering_ranges' => $ranges['data'] ?? []
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
