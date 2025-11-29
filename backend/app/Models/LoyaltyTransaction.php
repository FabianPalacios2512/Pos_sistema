<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'invoice_id',
        'points',
        'type',
        'description',
        'balance_after',
        'created_by'
    ];

    protected $casts = [
        'points' => 'integer',
        'balance_after' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relación con el cliente
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Relación con la factura
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Relación con el usuario que creó la transacción
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope para transacciones de ganancia
     */
    public function scopeEarned($query)
    {
        return $query->where('type', 'earned');
    }

    /**
     * Scope para transacciones de redención
     */
    public function scopeRedeemed($query)
    {
        return $query->where('type', 'redeemed');
    }

    /**
     * Scope para un cliente específico
     */
    public function scopeForCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    /**
     * Registrar ganancia de puntos
     */
    public static function recordEarned($customerId, $points, $invoiceId = null, $description = null, $createdBy = null)
    {
        $customer = Customer::findOrFail($customerId);

        // Actualizar puntos del cliente
        $customer->loyalty_points += $points;
        $customer->save();

        // Crear transacción
        return self::create([
            'customer_id' => $customerId,
            'invoice_id' => $invoiceId,
            'points' => $points,
            'type' => 'earned',
            'description' => $description ?? "Puntos ganados por compra",
            'balance_after' => $customer->loyalty_points,
            'created_by' => $createdBy
        ]);
    }

    /**
     * Registrar redención de puntos
     */
    public static function recordRedeemed($customerId, $points, $invoiceId = null, $description = null, $createdBy = null)
    {
        $customer = Customer::findOrFail($customerId);

        // Verificar que tenga suficientes puntos
        if ($customer->loyalty_points < $points) {
            throw new \Exception('El cliente no tiene suficientes puntos');
        }

        // Actualizar puntos del cliente
        $customer->loyalty_points -= $points;
        $customer->save();

        // Crear transacción (puntos negativos para redención)
        return self::create([
            'customer_id' => $customerId,
            'invoice_id' => $invoiceId,
            'points' => -$points, // Negativo para indicar que se restaron
            'type' => 'redeemed',
            'description' => $description ?? "Puntos redimidos",
            'balance_after' => $customer->loyalty_points,
            'created_by' => $createdBy
        ]);
    }

    /**
     * Registrar ajuste manual de puntos
     */
    public static function recordAdjustment($customerId, $points, $description, $createdBy = null)
    {
        $customer = Customer::findOrFail($customerId);

        // Actualizar puntos del cliente
        $customer->loyalty_points += $points;
        $customer->save();

        // Crear transacción
        return self::create([
            'customer_id' => $customerId,
            'points' => $points,
            'type' => 'adjusted',
            'description' => $description,
            'balance_after' => $customer->loyalty_points,
            'created_by' => $createdBy
        ]);
    }
}
