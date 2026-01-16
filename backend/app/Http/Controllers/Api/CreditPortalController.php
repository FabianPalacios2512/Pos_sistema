<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * 🎯 Portal Público de Créditos
 *
 * Permite a los clientes consultar su estado de crédito sin autenticación.
 * Acceso via:
 * 1. ID de Crédito + Apellido (con fuzzy matching)
 * 2. Token de acceso directo (link en email/whatsapp)
 */
class CreditPortalController extends Controller
{
    /**
     * 🔐 Acceso via Token directo (desde link en email/whatsapp)
     */
    public function accessByToken(Request $request)
    {
        try {
            $token = $request->input('token');

            if (!$token || strlen($token) < 10) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token inválido'
                ], 400);
            }

            $customer = Customer::where('credit_access_token', $token)
                ->where('credit_active', true)
                ->first();

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Enlace inválido o crédito inactivo'
                ], 404);
            }

            return $this->buildCreditResponse($customer);

        } catch (\Exception $e) {
            Log::error('Error en acceso por token: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud'
            ], 500);
        }
    }

    /**
     * 🔑 Acceso via ID de Crédito + Apellido
     */
    public function accessByCredentials(Request $request)
    {
        try {
            $creditId = strtoupper(trim($request->input('credit_id', '')));
            $lastName = trim($request->input('last_name', ''));

            if (empty($creditId) || empty($lastName)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Por favor ingrese su ID de crédito y apellido'
                ], 400);
            }

            // Buscar cliente por credit_id
            $customer = Customer::where('credit_id', $creditId)
                ->where('credit_active', true)
                ->first();

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID de crédito no encontrado o inactivo'
                ], 404);
            }

            // 🧠 Fuzzy matching para el apellido
            if (!$this->matchLastName($customer->name, $lastName)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El apellido no coincide con el registro'
                ], 401);
            }

            return $this->buildCreditResponse($customer);

        } catch (\Exception $e) {
            Log::error('Error en acceso por credenciales: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud'
            ], 500);
        }
    }

    /**
     * 📄 Obtener detalle de una factura específica
     */
    public function getInvoiceDetail(Request $request)
    {
        try {
            $invoiceId = $request->input('invoice_id');
            $token = $request->input('token');
            $creditId = $request->input('credit_id');

            // Verificar acceso
            $customer = null;
            if ($token) {
                $customer = Customer::where('credit_access_token', $token)->where('credit_active', true)->first();
            } elseif ($creditId) {
                $customer = Customer::where('credit_id', strtoupper($creditId))->where('credit_active', true)->first();
            }

            if (!$customer) {
                return response()->json(['success' => false, 'message' => 'Acceso no autorizado'], 401);
            }

            // Obtener la factura
            $invoice = Invoice::where('id', $invoiceId)
                ->where('customer_id', $customer->id)
                ->with(['invoiceItems.product'])
                ->first();

            if (!$invoice) {
                return response()->json(['success' => false, 'message' => 'Factura no encontrada'], 404);
            }

            // Construir respuesta con detalle
            // Usar invoiceItems (relación) en lugar de items (columna JSON)
            $items = $invoice->invoiceItems->map(function ($item) {
                return [
                    'product_name' => $item->product?->name ?? $item->product_name ?? 'Producto',
                    'quantity' => floatval($item->quantity),
                    'unit_price' => floatval($item->unit_price),
                    'subtotal' => floatval($item->subtotal),
                    'discount' => floatval($item->discount ?? 0),
                    'total' => floatval($item->total ?? $item->subtotal)
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'number' => $invoice->number,
                    'date' => $invoice->date?->format('d M Y'),
                    'subtotal' => floatval($invoice->subtotal),
                    'surcharge' => floatval($invoice->surcharge_amount ?? 0),
                    'total' => floatval($invoice->total),
                    'status' => $invoice->status,
                    'items' => $items,
                    'notes' => $invoice->notes ?? null
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error obteniendo detalle de factura: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al obtener la factura'], 500);
        }
    }

    /**
     * 🧠 Algoritmo de Fuzzy Matching para apellidos
     *
     * Maneja casos como:
     * - Tildes: "Pérez" vs "Perez"
     * - Typos: "Patermina" vs "Paternina"
     * - Mayúsculas: "LOPEZ" vs "Lopez"
     * - Espacios: " Garcia " vs "Garcia"
     * - Caracteres especiales: "O'Brien" vs "OBrien"
     */
    private function matchLastName(string $fullName, string $inputLastName): bool
    {
        // Normalizar ambos
        $normalizedInput = $this->normalizeString($inputLastName);
        $normalizedFullName = $this->normalizeString($fullName);

        // Obtener palabras del nombre completo (posibles apellidos)
        $nameParts = preg_split('/\s+/', $normalizedFullName);

        // Si solo hay una palabra, comparar directamente
        if (count($nameParts) === 1) {
            return $this->isSimilar($nameParts[0], $normalizedInput);
        }

        // Buscar en todas las partes del nombre (el apellido puede estar en cualquier posición)
        foreach ($nameParts as $part) {
            if ($this->isSimilar($part, $normalizedInput)) {
                return true;
            }
        }

        // También intentar con los últimos 2 elementos combinados (apellido compuesto)
        if (count($nameParts) >= 2) {
            $lastTwo = implode('', array_slice($nameParts, -2));
            if ($this->isSimilar($lastTwo, $normalizedInput)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Normaliza string removiendo tildes, caracteres especiales, espacios
     */
    private function normalizeString(string $str): string
    {
        // Convertir a minúsculas
        $str = mb_strtolower($str, 'UTF-8');

        // Remover tildes y caracteres especiales
        $str = strtr($str, [
            'á' => 'a', 'à' => 'a', 'ä' => 'a', 'â' => 'a', 'ã' => 'a',
            'é' => 'e', 'è' => 'e', 'ë' => 'e', 'ê' => 'e',
            'í' => 'i', 'ì' => 'i', 'ï' => 'i', 'î' => 'i',
            'ó' => 'o', 'ò' => 'o', 'ö' => 'o', 'ô' => 'o', 'õ' => 'o',
            'ú' => 'u', 'ù' => 'u', 'ü' => 'u', 'û' => 'u',
            'ñ' => 'n',
            'ç' => 'c',
            "'" => '', "`" => '', "´" => '', '"' => '',
            '-' => '', '_' => ''
        ]);

        // Remover espacios extra
        $str = preg_replace('/\s+/', '', $str);

        return $str;
    }

    /**
     * Determina si dos strings son similares (permite typos)
     * Usa distancia de Levenshtein con tolerancia
     */
    private function isSimilar(string $str1, string $str2): bool
    {
        // Match exacto
        if ($str1 === $str2) {
            return true;
        }

        // Si uno contiene al otro
        if (str_contains($str1, $str2) || str_contains($str2, $str1)) {
            return true;
        }

        // Distancia de Levenshtein con tolerancia
        // Tolerancia: 1 error por cada 4 caracteres, mínimo 1
        $maxErrors = max(1, floor(strlen($str2) / 4));
        $distance = levenshtein($str1, $str2);

        return $distance <= $maxErrors;
    }

    /**
     * 📊 Construir respuesta con información del crédito
     */
    private function buildCreditResponse(Customer $customer): \Illuminate\Http\JsonResponse
    {
        // Obtener facturas a crédito
        $invoices = Invoice::where('customer_id', $customer->id)
            ->where('payment_method', 'credit')
            ->whereNotIn('status', ['cancelled', 'returned'])
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'number' => $invoice->number,
                    'date' => $invoice->date?->format('Y-m-d'),
                    'subtotal' => floatval($invoice->subtotal),
                    'surcharge' => floatval($invoice->surcharge_amount ?? 0),
                    'total' => floatval($invoice->total),
                    'status' => $invoice->status
                ];
            });

        // Obtener pagos/abonos
        $payments = \App\Models\CreditPayment::where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($payment) {
                return [
                    'date' => $payment->created_at?->format('Y-m-d'),
                    'amount' => floatval($payment->amount),
                    'method' => $payment->payment_method,
                    'reference' => $payment->reference
                ];
            });

        // 🎯 Usar los campos current_debt y subtotal_debt del cliente directamente
        // Las facturas a crédito tienen status='paid' (la venta se hizo)
        // El crédito/deuda es independiente del status de la factura
        $balance = floatval($customer->current_debt ?? 0); // Lo que debe pagar (con recargo)
        $subtotalDebt = floatval($customer->subtotal_debt ?? 0); // Sin recargo (para cupo)
        $creditLimit = floatval($customer->credit_limit ?? 0);

        // Calcular desglose de deuda
        $surcharge = $balance - $subtotalDebt;

        // DISPONIBLE = Cupo - Subtotal de productos pendientes (NO el total con recargo)
        // El recargo es ganancia del negocio, no cuenta contra el cupo del cliente
        $available = max(0, $creditLimit - $subtotalDebt);

        // Obtener el porcentaje de recargo del sistema
        $settings = \App\Models\SystemSetting::first();
        $surchargePercentage = $settings ? floatval($settings->credit_surcharge_percentage) : 10;

        return response()->json([
            'success' => true,
            'data' => [
                'customer' => [
                    'name' => $customer->name,
                    'credit_id' => $customer->credit_id,
                    'document' => $customer->document_type . ' ' . $customer->document_number
                ],
                'credit' => [
                    'limit' => $creditLimit,
                    'balance' => $balance, // Deuda total (productos + recargo)
                    'balance_breakdown' => [
                        'products' => $subtotalDebt,
                        'surcharge' => $surcharge,
                        'surcharge_percentage' => $surchargePercentage
                    ],
                    'available' => $available, // Cupo - subtotal productos
                    'status' => $balance > 0 ? ($available <= 0 ? 'exceeded' : 'active') : 'clear'
                ],
                'invoices' => $invoices,
                'payments' => $payments,
                'last_updated' => now()->format('Y-m-d H:i:s')
            ]
        ]);
    }

    /**
     * 🎫 Generar ID de crédito único
     * Formato: CRD-XXXXXX (6 dígitos secuenciales)
     */
    public static function generateCreditId(): string
    {
        $lastCustomer = Customer::whereNotNull('credit_id')
            ->orderByRaw("CAST(SUBSTRING(credit_id, 5) AS UNSIGNED) DESC")
            ->first();

        if ($lastCustomer && $lastCustomer->credit_id) {
            $lastNumber = (int) substr($lastCustomer->credit_id, 4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'CRD-' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * 🔐 Generar token de acceso único (corto para URLs amigables)
     */
    public static function generateAccessToken(): string
    {
        return Str::random(16);
    }
}
