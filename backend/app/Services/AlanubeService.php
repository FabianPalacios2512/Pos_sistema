<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * Servicio de integración con Alanube (Alegra) para Facturación Electrónica DIAN Colombia
 * 
 * Alanube es ~10x más rápido que Factus (~2s vs ~15s)
 * Documentación: https://e-provider-docs.alegra.com/
 */
class AlanubeService
{
    private string $baseUrl;
    private string $token;
    private int $timeout = 30;

    public function __construct()
    {
        // Determinar ambiente (sandbox o producción)
        $environment = env('ALANUBE_ENVIRONMENT', 'sandbox');
        
        if ($environment === 'production') {
            $this->baseUrl = 'https://api.alegra.com/e-provider/col/v1';
        } else {
            $this->baseUrl = 'https://sandbox-api.alegra.com/e-provider/col/v1';
        }
        
        $this->token = env('ALANUBE_TOKEN', '');
    }

    /**
     * Crear instancia del servicio
     */
    public static function create(): self
    {
        return new self();
    }

    /**
     * Verificar si Alanube está configurado
     */
    public function isConfigured(): bool
    {
        return !empty($this->token);
    }

    /**
     * Verificar si el tenant actual tiene Alanube habilitado
     */
    public function isEnabledForTenant(): bool
    {
        $settings = DB::table('system_settings')->first();
        return $settings && 
               $settings->electronic_invoice_provider === 'alanube' && 
               !empty($settings->alanube_company_id);
    }

    /**
     * Obtener el company_id del tenant actual
     */
    public function getTenantCompanyId(): ?string
    {
        $settings = DB::table('system_settings')->first();
        return $settings->alanube_company_id ?? null;
    }

    /**
     * ═══════════════════════════════════════════════════════════════════
     * PASO 1: Crear empresa en Alanube
     * ═══════════════════════════════════════════════════════════════════
     */
    public function createCompany(array $companyData): array
    {

        $payload = [
            'name' => $companyData['name'],
            'identification' => $companyData['nit'],
            'dv' => $companyData['dv'],
            'useAlegraCertificate' => true // Usar certificado de Alegra (más fácil)
        ];

        $response = Http::timeout($this->timeout)
            ->withToken($this->token)
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])
            ->post("{$this->baseUrl}/companies", $payload);

        $result = $response->json();

        if ($response->successful()) {
        } else {
            Log::error('❌ Alanube: Error creando empresa', [
                'status' => $response->status(),
                'errors' => $result
            ]);
        }

        return [
            'success' => $response->successful(),
            'status' => $response->status(),
            'data' => $result
        ];
    }

    /**
     * ═══════════════════════════════════════════════════════════════════
     * PASO 2: Ejecutar Set de Pruebas DIAN (Habilitación)
     * ═══════════════════════════════════════════════════════════════════
     */
    public function runTestSet(string $companyId, string $type = 'invoices'): array
    {

        // Government ID para sandbox DIAN
        $governmentId = env('ALANUBE_ENVIRONMENT', 'sandbox') === 'production'
            ? env('ALANUBE_GOVERNMENT_ID') // En producción, viene de la DIAN real
            : 'a70562e0-631e-4ceb-aa65-36887b57dc17'; // ID fijo para sandbox

        $payload = [
            'type' => $type,
            'governmentId' => $governmentId,
            'company' => [
                'id' => $companyId
            ]
        ];

        $response = Http::timeout(120) // Set de pruebas puede tardar
            ->withToken($this->token)
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])
            ->post("{$this->baseUrl}/test-sets", $payload);

        $result = $response->json();

        if ($response->successful()) {
        } else {
            Log::error('❌ Alanube: Error en set de pruebas', [
                'status' => $response->status(),
                'errors' => $result
            ]);
        }

        return [
            'success' => $response->successful(),
            'status' => $response->status(),
            'data' => $result
        ];
    }

    /**
     * ═══════════════════════════════════════════════════════════════════
     * PASO 3: Crear Factura Electrónica
     * ═══════════════════════════════════════════════════════════════════
     */
    public function createInvoice(array $invoiceData): array
    {
        $startTime = microtime(true);
        

        // Obtener datos del tenant
        $settings = DB::table('system_settings')->first();
        $companyId = $settings->alanube_company_id ?? null;

        if (!$companyId) {
            return [
                'success' => false,
                'error' => 'Empresa no configurada en Alanube'
            ];
        }

        // Construir payload según estructura de Alanube
        $payload = $this->buildInvoicePayload($invoiceData, $settings);

        $response = Http::timeout($this->timeout)
            ->withToken($this->token)
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])
            ->post("{$this->baseUrl}/invoices", $payload);

        $result = $response->json();
        $elapsed = round((microtime(true) - $startTime) * 1000);

        if ($response->successful()) {
            // Log completo de la respuesta para debug
            
            
            // Buscar URL de QR de DIAN si existe
            $qrUrl = $result['invoice']['qrCodeUrl'] 
                ?? $result['invoice']['qrUrl'] 
                ?? $result['invoice']['qr_url']
                ?? null;
            
            // Si no hay URL pero hay CUFE, construir URL de DIAN habilitación
            if (!$qrUrl && isset($result['invoice']['cufe'])) {
                $cufe = $result['invoice']['cufe'];
                // URL de habilitación DIAN (sandbox)
                $qrUrl = "https://catalogo-vpfe-hab.dian.gov.co/document/searchqr?documentkey={$cufe}";
            }

            return [
                'success' => true,
                'data' => [
                    'bill' => [
                        'cufe' => $result['invoice']['cufe'] ?? null,
                        'number' => $result['invoice']['fullNumber'] ?? null,
                        'qr' => $qrUrl ?? $result['invoice']['qrCodeContent'] ?? null,
                        'qr_image' => $this->generateQrDataUrl($result['invoice']['qrCodeContent'] ?? ''),
                        'status' => $result['invoice']['legalStatus'] ?? 'SENT',
                        'government_response' => $result['invoice']['governmentResponse'] ?? null,
                        'xml_url' => $result['files']['xml'] ?? null
                    ]
                ],
                'time_ms' => $elapsed
            ];
        } else {
            Log::error('❌ Alanube: Error creando factura', [
                'status' => $response->status(),
                'errors' => $result,
                'time_ms' => $elapsed
            ]);

            return [
                'success' => false,
                'status' => $response->status(),
                'errors' => $result['errors'] ?? [],
                'time_ms' => $elapsed
            ];
        }
    }

    /**
     * Construir payload de factura según formato Alanube
     */
    private function buildInvoicePayload(array $invoiceData, $settings): array
    {
        $invoiceNumber = $this->extractInvoiceNumber($invoiceData['number'] ?? '');
        
        // Datos de resolución DIAN (sandbox o producción)
        $resolution = $this->getResolutionData($settings);
        
        // En sandbox, usar datos de la empresa de pruebas de Alanube
        // En producción, usar datos del tenant
        $isSandbox = env('ALANUBE_ENVIRONMENT', 'sandbox') === 'sandbox';
        
        if ($isSandbox) {
            // Empresa de pruebas autorizada ante DIAN
            $companyData = [
                'id' => $settings->alanube_company_id,
                'organizationType' => 1, // Persona Jurídica
                'identificationType' => '31', // NIT
                'identificationNumber' => '900559088', // NIT de pruebas autorizado
                'dv' => '2',
                'name' => '105 POS Test S.A.S',
                'regimeCode' => 'R-99-PN',
                'email' => 'test@105pos.com',
                'address' => [
                    'address' => 'Calle 123 #45-67',
                    'city' => '11001',
                    'department' => '11',
                    'country' => 'CO'
                ]
            ];
        } else {
            // Producción: usar datos reales del tenant
            $companyData = [
                'id' => $settings->alanube_company_id,
                'organizationType' => 1,
                'identificationType' => '31',
                'identificationNumber' => $settings->company_document ?? '',
                'dv' => $settings->company_dv ?? '0',
                'name' => $settings->company_name ?? '',
                'regimeCode' => $settings->tax_regime ?? 'R-99-PN',
                'email' => $settings->company_email ?? '',
                'address' => [
                    'address' => $settings->company_address ?? '',
                    'city' => $settings->company_city_code ?? '11001',
                    'department' => $settings->company_department_code ?? '11',
                    'country' => 'CO'
                ]
            ];
        }
        
        return [
            'documentType' => '01', // Factura de Venta
            
            'resolution' => $resolution,
            
            'company' => $companyData,
            
            'number' => $invoiceNumber,
            'date' => date('Y-m-d'),
            'time' => date('H:i:s') . '-05:00',
            'operationType' => '10', // Estándar
            
            'additionalDocumentReference' => [
                'id' => 'REF-' . $invoiceNumber,
                'number' => (string)$invoiceNumber,
                'issueDate' => date('Y-m-d'),
                'type' => 'IV'
            ],
            
            'customer' => $this->buildCustomerData($invoiceData['customer'] ?? []),
            
            'items' => $this->buildItemsData($invoiceData['items'] ?? []),
            
            // Calcular totales basados en los items procesados
            'totalAmounts' => $this->calculateTotalAmounts($invoiceData),
            
            'payments' => [
                [
                    'paymentForm' => $this->mapPaymentForm($invoiceData['payment_method'] ?? 'cash'),
                    'paymentMethod' => $this->mapPaymentMethod($invoiceData['payment_method'] ?? 'cash'),
                    'paymentDueDate' => $invoiceData['due_date'] ?? date('Y-m-d'),
                    'amount' => $this->calculatePayableTotal($invoiceData)
                ]
            ]
        ];
    }
    
    /**
     * Calcular totales de la factura basados en items
     * Usa el IVA configurado en system_settings del tenant
     */
    private function calculateTotalAmounts(array $invoiceData): array
    {
        $items = $invoiceData['items'] ?? [];
        $subtotal = 0;
        $taxTotal = 0;
        $discountTotal = (float)($invoiceData['discount_amount'] ?? 0);
        
        // Obtener tasa de IVA del tenant
        $settings = \DB::table('system_settings')->first();
        $taxRate = (float)($settings->iva_percentage ?? 0) / 100;
        
        foreach ($items as $item) {
            $unitPrice = (float)($item['unit_price'] ?? 0);
            $quantity = (float)($item['quantity'] ?? 1);
            $lineSubtotal = $unitPrice * $quantity;
            $lineTax = $taxRate > 0 ? round($lineSubtotal * $taxRate, 2) : 0;
            
            $subtotal += $lineSubtotal;
            $taxTotal += $lineTax;
        }
        
        return [
            'grossTotal' => round($subtotal, 2),
            'taxableTotal' => round($subtotal, 2),
            'taxTotal' => round($taxTotal, 2),
            'discountTotal' => round($discountTotal, 2),
            'chargeTotal' => 0.00,
            'advanceTotal' => 0.00,
            'payableTotal' => round($subtotal + $taxTotal - $discountTotal, 2)
        ];
    }
    
    /**
     * Calcular total a pagar
     */
    private function calculatePayableTotal(array $invoiceData): float
    {
        $totals = $this->calculateTotalAmounts($invoiceData);
        return $totals['payableTotal'];
    }

    /**
     * Obtener datos de resolución DIAN
     */
    private function getResolutionData($settings): array
    {
        // En sandbox usamos resolución de pruebas
        if (env('ALANUBE_ENVIRONMENT', 'sandbox') === 'sandbox') {
            return [
                'resolutionNumber' => '18760000001',
                'prefix' => 'SETP',
                'minNumber' => 990000000,
                'maxNumber' => 995000000,
                'startDate' => '2019-01-19',
                'endDate' => '2030-01-19',
                'technicalKey' => 'fc8eac422eba16e22ffd8c6f94b3f40a6e38162c'
            ];
        }

        // En producción usar datos reales del tenant
        return [
            'resolutionNumber' => $settings->dian_resolution_number ?? '',
            'prefix' => $settings->dian_prefix ?? '',
            'minNumber' => (int)($settings->dian_min_number ?? 0),
            'maxNumber' => (int)($settings->dian_max_number ?? 0),
            'startDate' => $settings->dian_start_date ?? '',
            'endDate' => $settings->dian_end_date ?? '',
            'technicalKey' => $settings->dian_technical_key ?? ''
        ];
    }

    /**
     * Construir datos del cliente
     */
    private function buildCustomerData(array $customer): array
    {
        $documentType = strtolower($customer['document_type'] ?? 'cc');
        $document = $customer['document'] ?? '222222222222';
        
        // Calcular DV solo para NIT, para otros documentos usar '0'
        $dv = ($documentType === 'nit') 
            ? ($customer['dv'] ?? $this->calculateDV($document))
            : '0';
        
        return [
            'organizationType' => $documentType === 'nit' ? 1 : 2,
            'identificationType' => $this->mapDocumentType($documentType),
            'identificationNumber' => $document,
            'dv' => $dv,
            'name' => $customer['name'] ?? 'Consumidor Final',
            'email' => $customer['email'] ?? '',
            'address' => [
                'address' => $customer['address'] ?? 'Sin dirección',
                'city' => '11001',
                'department' => '11',
                'country' => 'CO'
            ],
            'regimeCode' => 'R-99-PN'
        ];
    }
    
    /**
     * Calcular Dígito de Verificación (DV) para NIT colombiano
     * Algoritmo oficial de la DIAN
     */
    private function calculateDV(string $nit): string
    {
        // Limpiar el NIT (solo números)
        $nit = preg_replace('/[^0-9]/', '', $nit);
        
        // Factores de ponderación DIAN
        $factors = [71, 67, 59, 53, 47, 43, 41, 37, 29, 23, 19, 17, 13, 7, 3];
        
        // Rellenar con ceros a la izquierda hasta 15 dígitos
        $nit = str_pad($nit, 15, '0', STR_PAD_LEFT);
        
        $sum = 0;
        for ($i = 0; $i < 15; $i++) {
            $sum += (int)$nit[$i] * $factors[$i];
        }
        
        $remainder = $sum % 11;
        
        if ($remainder == 0) return '0';
        if ($remainder == 1) return '1';
        
        return (string)(11 - $remainder);
    }

    /**
     * Construir datos de items
     * Usa el IVA configurado en system_settings del tenant
     */
    private function buildItemsData(array $items): array
    {
        // Obtener tasa de IVA del tenant
        $settings = \DB::table('system_settings')->first();
        $taxRate = (float)($settings->iva_percentage ?? 0);
        
        return array_map(function($item, $index) use ($taxRate) {
            $unitPrice = (float)($item['unit_price'] ?? 0);
            $quantity = (float)($item['quantity'] ?? 1);
            $subtotal = $unitPrice * $quantity;
            $taxAmount = $taxRate > 0 ? round($subtotal * ($taxRate / 100), 2) : 0;

            $itemData = [
                'code' => $item['product_sku'] ?? 'PROD-' . ($index + 1),
                'description' => $item['product_name'] ?? 'Producto',
                'quantity' => $quantity,
                'unitCode' => '94', // Unidad
                'unitPrice' => $unitPrice,
                'price' => $unitPrice,
                'subtotal' => $subtotal,
                'discount' => (float)($item['discount_amount'] ?? 0),
                'taxAmount' => $taxAmount,
                'total' => $subtotal + $taxAmount
            ];
            
            // Siempre incluir array de taxes - si no hay IVA, usar código de excluido
            if ($taxRate > 0) {
                // Con IVA
                $itemData['taxes'] = [
                    [
                        'taxCode' => '01', // IVA
                        'taxPercentage' => number_format($taxRate, 2, '.', ''),
                        'taxRate' => number_format($taxRate, 2, '.', ''),
                        'taxableAmount' => $subtotal,
                        'taxAmount' => $taxAmount
                    ]
                ];
            } else {
                // Sin IVA - Excluido (código ZZ o sin impuestos)
                // Para la DIAN, productos sin IVA aún necesitan declarar base imponible
                $itemData['taxes'] = [
                    [
                        'taxCode' => '01', // IVA
                        'taxPercentage' => '0.00',
                        'taxRate' => '0.00',
                        'taxableAmount' => $subtotal,
                        'taxAmount' => 0.00
                    ]
                ];
            }
            
            return $itemData;
        }, $items, array_keys($items));
    }

    /**
     * Mapear tipo de documento a código DIAN
     */
    private function mapDocumentType(string $type): string
    {
        $map = [
            'cc' => '13',      // Cédula de Ciudadanía
            'nit' => '31',     // NIT
            'ce' => '22',      // Cédula de Extranjería
            'passport' => '41', // Pasaporte
            'ti' => '12',      // Tarjeta de Identidad
        ];
        return $map[strtolower($type)] ?? '13';
    }

    /**
     * Mapear forma de pago
     */
    private function mapPaymentForm(string $method): string
    {
        // 1 = Contado, 2 = Crédito
        return in_array($method, ['credit', 'creditienda']) ? '2' : '1';
    }

    /**
     * Mapear método de pago
     */
    private function mapPaymentMethod(string $method): string
    {
        $map = [
            'cash' => '10',           // Efectivo
            'efectivo' => '10',
            'card' => '48',           // Tarjeta
            'tarjeta' => '48',
            'credit_card' => '48',
            'debit_card' => '48',
            'transfer' => '31',       // Transferencia
            'transferencia' => '31',
            'nequi' => '31',
            'daviplata' => '31',
            'credit' => '30',         // Crédito
            'creditienda' => '30',
        ];
        return $map[strtolower($method)] ?? '10';
    }

    /**
     * Extraer número de factura del código completo
     * En sandbox, usar número dentro del rango autorizado DIAN
     */
    private function extractInvoiceNumber(string $fullNumber): int
    {
        // Extraer solo números del final (ej: "FACT-000039" -> 39)
        preg_match('/(\d+)$/', $fullNumber, $matches);
        $localNumber = (int)($matches[1] ?? 1);
        
        // En sandbox, los números deben estar en el rango 990000000 - 995000000
        if (env('ALANUBE_ENVIRONMENT', 'sandbox') === 'sandbox') {
            // Generar número único dentro del rango usando timestamp + número local
            return 990000000 + (time() % 4000000) + $localNumber;
        }
        
        return $localNumber;
    }

    /**
     * Generar QR en formato data URL
     */
    private function generateQrDataUrl(string $qrContent): string
    {
        if (empty($qrContent)) {
            return '';
        }

        // Extraer URL del QR del contenido
        if (preg_match('/QRCode:\s*(https?:\/\/[^\s]+)/', $qrContent, $matches)) {
            $qrUrl = $matches[1];
            
            // Usar servicio de QR para generar imagen
            // En producción podrías usar una librería PHP como endroid/qr-code
            return 'data:image/png;base64,' . base64_encode(
                file_get_contents("https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($qrUrl))
            );
        }

        return '';
    }

    /**
     * Consultar estado de una factura
     */
    public function getInvoiceStatus(string $trackId): array
    {
        $response = Http::timeout($this->timeout)
            ->withToken($this->token)
            ->withHeaders(['Accept' => 'application/json'])
            ->get("{$this->baseUrl}/dian/{$trackId}");

        return [
            'success' => $response->successful(),
            'data' => $response->json()
        ];
    }

    /**
     * Obtener resoluciones disponibles para una empresa
     */
    public function getResolutions(string $companyId): array
    {
        $response = Http::timeout($this->timeout)
            ->withToken($this->token)
            ->withHeaders(['Accept' => 'application/json'])
            ->get("{$this->baseUrl}/resolutions", [
                'company' => $companyId
            ]);

        return [
            'success' => $response->successful(),
            'data' => $response->json()
        ];
    }
}