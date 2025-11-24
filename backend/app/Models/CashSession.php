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
        'opening_date',
        'opening_time',
        'opening_amount',
        'opening_notes',
        'closing_date',
        'closing_time',
        'expected_amount',
        'actual_amount',
        'difference',
        'closing_notes',
        'closing_status',
        'expenses_detail',
        'closing_breakdown',
        'status',
        'total_sales',
        'total_expenses',
        'cash_sales',
        'card_sales',
        'transfer_sales'
    ];

    protected $casts = [
        'opening_date' => 'date',
        'closing_date' => 'date',
        'opening_amount' => 'decimal:2',
        'expected_amount' => 'decimal:2',
        'actual_amount' => 'decimal:2',
        'difference' => 'decimal:2',
        'total_sales' => 'decimal:2',
        'total_expenses' => 'decimal:2',
        'cash_sales' => 'decimal:2',
        'card_sales' => 'decimal:2',
        'transfer_sales' => 'decimal:2',
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
            $this->difference = $this->actual_amount - $this->expected_amount;
        }
        return $this->difference;
    }

    /**
     * Actualizar totales de ventas basado en facturas con métodos de pago
     */
    public function updateSalesTotals()
    {
        // Obtener totales por método de pago desde invoices
        $invoiceTotals = $this->invoices()
            ->selectRaw('
                payment_method,
                SUM(total) as total_amount,
                COUNT(*) as count
            ')
            ->groupBy('payment_method')
            ->get();

        // Inicializar totales
        $cashSales = 0;
        $cardSales = 0;
        $transferSales = 0;

        // Categorizar por método de pago
        foreach ($invoiceTotals as $total) {
            $amount = $total->total_amount ?? 0;

            switch (strtolower($total->payment_method)) {
                case 'cash':
                case 'efectivo':
                    $cashSales += $amount;
                    break;
                case 'card':
                case 'tarjeta':
                case 'credito':
                case 'debito':
                    $cardSales += $amount;
                    break;
                case 'transfer':
                case 'transferencia':
                case 'nequi':
                case 'daviplata':
                case 'bancolombia':
                    $transferSales += $amount;
                    break;
                default:
                    // Si no se reconoce el método, asumimos efectivo
                    $cashSales += $amount;
                    break;
            }
        }

        // Actualizar campos
        $this->cash_sales = $cashSales;
        $this->card_sales = $cardSales;
        $this->transfer_sales = $transferSales;
        $this->total_sales = $cashSales + $cardSales + $transferSales;

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
            'opening_date' => now()->toDateString(),
            'opening_time' => now()->toTimeString(),
            'opening_amount' => $openingAmount,
            'opening_notes' => $notes,
            'status' => self::STATUS_OPEN,
            'total_sales' => 0,
            'total_expenses' => 0,
            'cash_sales' => 0,
            'card_sales' => 0,
            'transfer_sales' => 0
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
        $this->closing_date = now()->toDateString();
        $this->closing_time = now()->toTimeString();
        $this->closing_notes = $notes;
        $this->status = self::STATUS_CLOSED;

        $this->save();

        return $this;
    }
}
