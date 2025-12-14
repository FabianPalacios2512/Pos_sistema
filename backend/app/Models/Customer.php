<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'city',
        'document_type',
        'document_number',
        'birth_date',
        'gender',
        'credit_limit',
        'current_debt',
        'total_purchases',
        'total_orders',
        'active',
        'credit_active',
        'last_purchase',
        'loyalty_points'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'credit_limit' => 'decimal:2',
        'current_debt' => 'decimal:2',
        'total_purchases' => 'decimal:2',
        'total_orders' => 'integer',
        'active' => 'boolean',
        'credit_active' => 'boolean',
        'last_purchase' => 'datetime',
        'loyalty_points' => 'integer'
    ];

    protected $appends = [
        'loyalty_points_value'
    ];

    // Relaciones
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function loyaltyTransactions()
    {
        return $this->hasMany(LoyaltyTransaction::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    // Métodos auxiliares
    public function getAvailableCreditAttribute()
    {
        return $this->credit_limit - $this->current_debt;
    }

    public function hasCredit($amount = 0)
    {
        return $this->available_credit >= $amount;
    }

    public function getAgeAttribute()
    {
        return $this->birth_date ? $this->birth_date->age : null;
    }

    public function updateTotals()
    {
        $completedSales = $this->sales()->where('status', 'completed');
        $this->total_purchases = $completedSales->sum('total_amount');
        $this->total_orders = $completedSales->count();
        $this->last_purchase = $completedSales->latest('sale_date')->first()?->sale_date;
        $this->save();
    }

    /**
     * Calcular cuántos puntos se ganarán por un monto de compra
     */
    public static function calculatePointsToEarn($amount)
    {
        $settings = \App\Models\SystemSetting::first();

        if (!$settings || !$settings->enable_loyalty_system) {
            return 0;
        }

        $pointsPerCurrency = $settings->loyalty_points_per_currency ?? 0.001;
        return floor($amount * $pointsPerCurrency);
    }

    /**
     * Calcular cuánto dinero vale un número de puntos
     */
    public static function calculatePointsValue($points)
    {
        $settings = \App\Models\SystemSetting::first();

        if (!$settings || !$settings->enable_loyalty_system) {
            return 0;
        }

        $pointValue = $settings->loyalty_point_value ?? 10;
        return $points * $pointValue;
    }

    /**
     * Obtener el valor en dinero de los puntos del cliente
     */
    public function getLoyaltyPointsValueAttribute()
    {
        return self::calculatePointsValue($this->loyalty_points);
    }

    /**
     * Verificar si el cliente tiene suficientes puntos
     */
    public function hasLoyaltyPoints($points)
    {
        return $this->loyalty_points >= $points;
    }

    /**
     * Ganar puntos por una compra
     */
    public function earnLoyaltyPoints($amount, $invoiceId = null, $createdBy = null)
    {
        $points = self::calculatePointsToEarn($amount);

        if ($points > 0) {
            return LoyaltyTransaction::recordEarned(
                $this->id,
                $points,
                $invoiceId,
                "Ganaste {$points} puntos por compra de $" . number_format($amount, 0),
                $createdBy
            );
        }

        return null;
    }

    /**
     * Redimir puntos
     */
    public function redeemLoyaltyPoints($points, $invoiceId = null, $createdBy = null)
    {
        if (!$this->hasLoyaltyPoints($points)) {
            throw new \Exception('No tienes suficientes puntos');
        }

        $value = self::calculatePointsValue($points);

        return LoyaltyTransaction::recordRedeemed(
            $this->id,
            $points,
            $invoiceId,
            "Redimiste {$points} puntos por $" . number_format($value, 0),
            $createdBy
        );
    }
}
