<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'opened_at',
        'opening_amount',
        'closed_at',
        'closing_amount',
        'expected_amount',
        'actual_amount',
        'difference_amount',
        'closing_notes',
        'closing_status',
        'expenses_detail',
        'closing_breakdown',
        'status'
    ];

    protected $appends = [
        'total_sales',
        'cash_sales',
        'card_sales',
        'transfer_sales'
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'opening_amount' => 'decimal:2',
        'closing_amount' => 'decimal:2',
        'expected_amount' => 'decimal:2',
        'actual_amount' => 'decimal:2',
        'difference_amount' => 'decimal:2',
        'closing_breakdown' => 'array',
    ];

    const STATUS_OPEN = 'open';
    const STATUS_CLOSED = 'closed';

    /**
     * Relación con el usuario que maneja la caja
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Facturas asociadas a esta sesión de caja
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Ventas asociadas a esta sesión de caja (si existe la relación)
     */
    /*
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
    */

    /**
     * Scope para sesiones abiertas
     */
    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    /**
     * Scope para sesiones cerradas
     */
    public function scopeClosed($query)
    {
        return $query->where('status', self::STATUS_CLOSED);
    }

    /**
     * Scope para sesiones del usuario
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Obtener la sesión abierta del usuario
     */
    public static function getOpenSessionForUser($userId)
    {
        return self::where('user_id', $userId)
                   ->where('status', self::STATUS_OPEN)
                   ->first();
    }

    /**
     * Verificar si el usuario tiene una sesión abierta
     */
    public static function hasOpenSession($userId)
    {
        return self::where('user_id', $userId)
                   ->where('status', self::STATUS_OPEN)
                   ->exists();
    }

    /**
     * Calcular el monto esperado en caja
     */
    public function calculateExpectedAmount()
    {
        $this->expected_amount = $this->opening_amount + $this->cash_sales - $this->total_expenses;
        return $this->expected_amount;
    }

    /**
     * Calcular la diferencia entre esperado y actual
     */
    public function calculateDifference()
    {
        if ($this->actual_amount !== null && $this->expected_amount !== null) {
            $this->difference_amount = $this->actual_amount - $this->expected_amount;
        }
        return $this->difference_amount;
    }

    /**
     * Accessor para obtener el total de ventas desde closing_breakdown
     */
    public function getTotalSalesAttribute()
    {
        return $this->closing_breakdown['sales']['total'] ?? 0;
    }

    /**
     * Accessor para obtener el total de ventas en efectivo desde closing_breakdown
     */
    public function getCashSalesAttribute()
    {
        return $this->closing_breakdown['sales']['cash'] ?? 0;
    }

    /**
     * Accessor para obtener el total de ventas con tarjeta desde closing_breakdown
     */
    public function getCardSalesAttribute()
    {
        return $this->closing_breakdown['sales']['card'] ?? 0;
    }

    /**
     * Accessor para obtener el total de ventas por transferencia desde closing_breakdown
     */
    public function getTransferSalesAttribute()
    {
        return $this->closing_breakdown['sales']['transfer'] ?? 0;
    }

    /**
     * Actualizar totales de ventas basado en facturas con métodos de pago
     */
    public function updateSalesTotals()
    {
        // NOTA: La tabla 'invoices' en el VPS no tiene la columna 'payment_method'.
        // Por lo tanto, no podemos agrupar por método de pago.
        // Asumiremos temporalmente que todo es efectivo o simplemente sumaremos el total.

        /*
        // Código original que falla por falta de columna payment_method
        $invoiceTotals = $this->invoices()
            ->selectRaw('
                payment_method,
                SUM(total) as total_amount,
                COUNT(*) as count
            ')
            ->groupBy('payment_method')
            ->get();
        */

        // Solución temporal: Sumar todo el total de las facturas asociadas
        $totalSales = $this->invoices()->sum('total');

        // Inicializar totales (Asumimos todo como efectivo por defecto para evitar errores)
        $cashSales = $totalSales;
        $cardSales = 0;
        $transferSales = 0;

        /*
        // Categorizar por método de pago (Deshabilitado hasta que exista la columna)
        foreach ($invoiceTotals as $total) {
            // ...
        }
        */

        // Actualizar closing_breakdown en lugar de columnas inexistentes
        $breakdown = $this->closing_breakdown ?? [];
        $breakdown['sales'] = [
            'total' => $cashSales + $cardSales + $transferSales,
            'cash' => $cashSales,
            'card' => $cardSales,
            'transfer' => $transferSales
        ];

        $this->closing_breakdown = $breakdown;

        return $this;
    }

    /**
     * Abrir una nueva sesión de caja
     */
    public static function openSession($userId, $openingAmount, $notes = null)
    {
        // Verificar que no hay sesión abierta
        if (self::hasOpenSession($userId)) {
            throw new \Exception('El usuario ya tiene una sesión de caja abierta');
        }

        return self::create([
            'user_id' => $userId,
            'opened_at' => now(),
            'opening_amount' => $openingAmount,
            // 'opening_notes' => $notes, // Columna no existe en DB
            'status' => self::STATUS_OPEN,
            'total_expenses' => 0,
            'closing_breakdown' => [
                'sales' => [
                    'total' => 0,
                    'cash' => 0,
                    'card' => 0,
                    'transfer' => 0
                ]
            ]
        ]);
    }

    /**
     * Cerrar la sesión de caja
     */
    public function closeSession($actualAmount, $notes = null)
    {
        if ($this->status === self::STATUS_CLOSED) {
            throw new \Exception('La sesión de caja ya está cerrada');
        }

        // Actualizar totales antes de cerrar
        $this->updateSalesTotals();

        // Calcular monto esperado y diferencia
        $this->calculateExpectedAmount();
        $this->actual_amount = $actualAmount;
        $this->calculateDifference();

        // Establecer datos de cierre
        $this->closed_at = now();
        $this->closing_notes = $notes;
        $this->status = self::STATUS_CLOSED;

        $this->save();

        return $this;
    }
}
