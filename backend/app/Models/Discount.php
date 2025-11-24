<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'code',
        'type',
        'value',
        'applies_to',
        'applicable_items',
        'minimum_amount',
        'maximum_discount',
        'usage_limit',
        'used_count',
        'starts_at',
        'expires_at',
        'active',
        'stackable',
        'allow_multiple_uses_per_user',
    ];

    protected $casts = [
        'applicable_items' => 'array',
        'active' => 'boolean',
        'stackable' => 'boolean',
        'allow_multiple_uses_per_user' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'value' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
        'maximum_discount' => 'decimal:2',
    ];

    /**
     * Relación con descuentos aplicados en facturas
     */
    public function invoiceDiscounts()
    {
        return $this->hasMany(InvoiceDiscount::class);
    }

    /**
     * Relación con los usos del descuento
     */
    public function usages()
    {
        return $this->hasMany(DiscountUsage::class);
    }

    /**
     * Verificar si el descuento está activo y vigente
     */
    public function isValid()
    {
        if (!$this->active) {
            return false;
        }

        $now = Carbon::now();

        if ($this->starts_at && $now->isBefore($this->starts_at)) {
            return false;
        }

        if ($this->expires_at && $now->isAfter($this->expires_at)) {
            return false;
        }

        if ($this->usage_limit) {
            // Usar conteo real de la tabla discount_usages en lugar del campo used_count
            $realUsageCount = $this->usages()->count();
            if ($realUsageCount >= $this->usage_limit) {
                return false;
            }
        }

        return true;
    }

    /**
     * Verificar si el descuento puede ser usado por un usuario específico
     */
    public function canBeUsedBy($userIdentifier = null, $customerEmail = null, $customerPhone = null)
    {
        // Primero verificar validez general
        if (!$this->isValid()) {
            return false;
        }

        // Si permite múltiples usos por usuario, solo verificar validez general
        if ($this->allow_multiple_uses_per_user) {
            return true;
        }

        // Si no se requiere identificación de usuario, permitir uso
        if (!$userIdentifier && !$customerEmail && !$customerPhone) {
            return true;
        }

        // Verificar si el usuario ya ha usado este descuento
        // Priorizar user_identifier, pero también verificar por email y teléfono como respaldo

        $hasUsed = false;

        // 1. Verificar por user_identifier (más confiable)
        if ($userIdentifier) {
            $hasUsed = $this->usages()->where('user_identifier', $userIdentifier)->exists();
        }

        // 2. Si no se encontró por user_identifier, verificar por email y teléfono (para compatibilidad)
        if (!$hasUsed && ($customerEmail || $customerPhone)) {
            $query = $this->usages();

            if ($customerEmail && $customerPhone) {
                // Si tenemos ambos, usar AND
                $hasUsed = $query->where('customer_email', $customerEmail)
                               ->where('customer_phone', $customerPhone)
                               ->exists();
            } elseif ($customerEmail) {
                // Solo email
                $hasUsed = $query->where('customer_email', $customerEmail)->exists();
            } elseif ($customerPhone) {
                // Solo teléfono
                $hasUsed = $query->where('customer_phone', $customerPhone)->exists();
            }
        }

        // Si ya lo usó y no permite múltiples usos, no puede usarlo de nuevo
        return !$hasUsed;
    }

    /**
     * Contar cuántas veces ha usado este descuento un usuario
     */
    public function getUsageCountBy($userIdentifier = null, $customerEmail = null, $customerPhone = null)
    {
        $query = $this->usages();

        if ($userIdentifier) {
            $query->where('user_identifier', $userIdentifier);
        }

        if ($customerEmail) {
            $query->orWhere('customer_email', $customerEmail);
        }

        if ($customerPhone) {
            $query->orWhere('customer_phone', $customerPhone);
        }

        return $query->count();
    }

    /**
     * Calcular el monto de descuento para un total dado
     */
    public function calculateDiscount($total)
    {
        if (!$this->isValid() || $total < ($this->minimum_amount ?? 0)) {
            return 0;
        }

        $discount = 0;

        if ($this->type === 'percentage') {
            $discount = $total * ($this->value / 100);
        } else {
            $discount = $this->value;
        }

        // Aplicar límite máximo si existe
        if ($this->maximum_discount && $discount > $this->maximum_discount) {
            $discount = $this->maximum_discount;
        }

        return round($discount, 2);
    }

    /**
     * Aplicar el descuento (incrementar contador de uso)
     */
    public function apply()
    {
        $this->increment('used_count');
    }

    /**
     * Registrar el uso del descuento por un usuario específico
     */
    public function recordUsage($discountAmount, $userIdentifier = null, $customerEmail = null, $customerPhone = null, $invoiceNumber = null, $metadata = [])
    {
        // Registrar el uso individual
        $this->usages()->create([
            'user_identifier' => $userIdentifier,
            'customer_email' => $customerEmail,
            'customer_phone' => $customerPhone,
            'invoice_number' => $invoiceNumber,
            'discount_amount' => $discountAmount,
            'usage_metadata' => $metadata,
            'used_at' => Carbon::now()
        ]);

        // Incrementar contador general
        $this->increment('used_count');
    }

    /**
     * Generar código aleatorio
     */
    public static function generateCode($length = 8)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }

        // Verificar que no exista
        while (static::where('code', $code)->exists()) {
            $code = static::generateCode($length);
        }

        return $code;
    }

    /**
     * Scope para descuentos activos
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope para descuentos vigentes
     */
    public function scopeValid($query)
    {
        $now = Carbon::now();

        return $query->where('active', true)
                    ->where(function ($q) use ($now) {
                        $q->whereNull('starts_at')
                          ->orWhere('starts_at', '<=', $now);
                    })
                    ->where(function ($q) use ($now) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>=', $now);
                    })
                    ->where(function ($q) {
                        $q->whereNull('usage_limit')
                          ->orWhereRaw('used_count < usage_limit');
                    });
    }
}
