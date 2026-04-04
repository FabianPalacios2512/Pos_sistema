<?php

namespace App\Http\Controllers;

use App\Services\FactusService;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * Controlador para facturación electrónica con Factus (DIAN Colombia)
 * 
 * NOTA: Las credenciales son GLOBALES (desde .env)
 * 105POS compra paquete de facturas y las distribuye entre tenants
 * Los tenants NO configuran credenciales
 */
class FactusController extends Controller
{
    /**
     * Verificar estado de Factus (configuración GLOBAL desde .env)
     */
    public function status(): JsonResponse
    {
        try {
            $factus = FactusService::create();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'enabled' => $factus->isEnabled(),
                    'configured' => $factus->isConfigured(),
                    'sandbox' => env('FACTUS_SANDBOX', true)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al verificar configuración: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Probar conexión con Factus
     */
    public function testConnection(): JsonResponse
    {
        try {
            $factus = FactusService::create();
            
            if (!$factus->isConfigured()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Factus no está configurado. Configure las credenciales en el archivo .env'
                ], 400);
            }
            
            $result = $factus->testConnection();
            
            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'data' => [
                    'numbering_ranges' => $result['numbering_ranges'] ?? []
                ]
            ], $result['success'] ? 200 : 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de conexión: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Obtener rangos de numeración disponibles
     */
    public function getNumberingRanges(): JsonResponse
    {
        try {
            $factus = FactusService::create();
            
            if (!$factus->isConfigured()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Factus no está configurado'
                ], 400);
            }
            
            $ranges = $factus->getNumberingRanges();
            
            return response()->json([
                'success' => true,
                'data' => $ranges['data'] ?? []
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener rangos: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Validar factura ante la DIAN a través de Factus
     */
    public function validateInvoice(Request $request, $invoiceId): JsonResponse
    {
        try {
            $factus = FactusService::create();
            
            // Verificar que Factus esté habilitado
            if (!$factus->isEnabled()) {
                return response()->json([
                    'success' => false,
                    'message' => 'La facturación electrónica no está habilitada'
                ], 400);
            }
            
            // Obtener la factura
            $invoice = Invoice::with(['customer', 'items.product'])->find($invoiceId);
            
            if (!$invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Factura no encontrada'
                ], 404);
            }
            
            // Verificar que no esté ya validada
            if (!empty($invoice->cufe)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta factura ya fue validada ante la DIAN',
                    'data' => [
                        'cufe' => $invoice->cufe,
                        'factus_number' => $invoice->factus_number
                    ]
                ], 400);
            }
            
            // Preparar datos para Factus
            $invoiceData = [
                'reference_code' => $invoice->number,
                'number' => $invoice->number,
                'payment_method' => $invoice->payment_method,
                'notes' => $invoice->notes,
                'send_email' => $request->input('send_email', false),
                'due_date' => $invoice->due_date?->format('Y-m-d'),
                'subtotal' => (float) $invoice->subtotal,
                'discount_amount' => (float) ($invoice->discount_amount ?? 0),
                'customer' => [
                    'name' => $invoice->customer->name ?? 'Consumidor Final',
                    'document' => $invoice->customer->document ?? '222222222222',
                    'document_type' => $invoice->customer->document_type ?? 'cc',
                    'email' => $invoice->customer->email ?? '',
                    'phone' => $invoice->customer->phone ?? '',
                    'address' => $invoice->customer->address ?? 'Sin dirección'
                ],
                'items' => $invoice->items->map(function($item) {
                    return [
                        'product_id' => $item->product_id,
                        'product_sku' => $item->product_sku ?? $item->product?->sku ?? '',
                        'product_name' => $item->product_name ?? $item->product?->name ?? 'Producto',
                        'quantity' => $item->quantity,
                        'unit_price' => (float) $item->unit_price,
                        'discount_amount' => (float) ($item->discount_amount ?? 0),
                        'tax_amount' => (float) ($item->tax_amount ?? 0),
                        'notes' => $item->notes ?? ''
                    ];
                })->toArray()
            ];
            
            // Enviar a Factus
            $response = $factus->createInvoice($invoiceData);
            
            // Extraer datos de la respuesta
            $billData = $response['data']['bill'] ?? [];
            
            // Actualizar la factura con los datos de validación
            $invoice->update([
                'cufe' => $billData['cufe'] ?? null,
                'factus_number' => $billData['number'] ?? null,
                'qr_code' => $billData['qr'] ?? null,
                'qr_image' => $billData['qr_image'] ?? null,
                'factus_status' => 'validated',
                'factus_validated_at' => now(),
                'factus_response' => $response
            ]);
            
            Log::info('✅ Factura validada ante DIAN', [
                'invoice_id' => $invoice->id,
                'number' => $invoice->number,
                'cufe' => $billData['cufe'] ?? 'N/A',
                'factus_number' => $billData['number'] ?? 'N/A'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Factura validada correctamente ante la DIAN',
                'data' => [
                    'cufe' => $billData['cufe'] ?? null,
                    'factus_number' => $billData['number'] ?? null,
                    'qr_code' => $billData['qr'] ?? null,
                    'qr_image' => $billData['qr_image'] ?? null
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ Error validando factura con Factus', [
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al validar la factura: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Descargar PDF de factura validada
     */
    public function downloadPDF($invoiceId): JsonResponse
    {
        try {
            $invoice = Invoice::find($invoiceId);
            
            if (!$invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Factura no encontrada'
                ], 404);
            }
            
            if (empty($invoice->factus_number)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta factura no ha sido validada ante la DIAN'
                ], 400);
            }
            
            $factus = FactusService::create();
            $response = $factus->downloadPDF($invoice->factus_number);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'file_name' => $response['data']['file_name'] ?? 'factura.pdf',
                    'pdf_base64' => $response['data']['pdf_base_64_encoded'] ?? ''
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al descargar PDF: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Descargar XML de factura validada
     */
    public function downloadXML($invoiceId): JsonResponse
    {
        try {
            $invoice = Invoice::find($invoiceId);
            
            if (!$invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Factura no encontrada'
                ], 404);
            }
            
            if (empty($invoice->factus_number)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta factura no ha sido validada ante la DIAN'
                ], 400);
            }
            
            $factus = FactusService::create();
            $response = $factus->downloadXML($invoice->factus_number);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'file_name' => $response['data']['file_name'] ?? 'factura.xml',
                    'xml_base64' => $response['data']['xml_base_64_encoded'] ?? ''
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al descargar XML: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Obtener estado de validación de una factura
     */
    public function getInvoiceStatus($invoiceId): JsonResponse
    {
        try {
            $invoice = Invoice::find($invoiceId);
            
            if (!$invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Factura no encontrada'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'invoice_number' => $invoice->number,
                    'validated' => !empty($invoice->cufe),
                    'cufe' => $invoice->cufe,
                    'factus_number' => $invoice->factus_number,
                    'qr_code' => $invoice->qr_code,
                    'qr_image' => $invoice->qr_image,
                    'factus_status' => $invoice->factus_status,
                    'validated_at' => $invoice->factus_validated_at
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Listar facturas con estado de validación DIAN
     */
    public function listValidatedInvoices(Request $request): JsonResponse
    {
        try {
            $query = Invoice::with('customer')
                ->whereNotNull('cufe')
                ->orderBy('factus_validated_at', 'desc');
            
            // Filtros opcionales
            if ($request->has('from_date')) {
                $query->whereDate('date', '>=', $request->from_date);
            }
            
            if ($request->has('to_date')) {
                $query->whereDate('date', '<=', $request->to_date);
            }
            
            $invoices = $query->get()->map(function($invoice) {
                return [
                    'id' => $invoice->id,
                    'number' => $invoice->number,
                    'factus_number' => $invoice->factus_number,
                    'cufe' => $invoice->cufe,
                    'customer_name' => $invoice->customer->name ?? 'N/A',
                    'total' => (float) $invoice->total,
                    'date' => $invoice->date->format('Y-m-d'),
                    'validated_at' => $invoice->factus_validated_at,
                    'qr_code' => $invoice->qr_code
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $invoices
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
