<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'document',
        'email',
        'phone',
        'address',
        'contact_person',
        'payment_terms',
        'credit_limit',
        'current_debt',
        'total_orders',
        'total_purchased',
        'avg_delivery_days',
        'fulfillment_rate',
        'last_order_date',
        'last_delivery_date',
        'active',
        'notes'
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'current_debt' => 'decimal:2',
        'total_purchased' => 'decimal:2',
        'avg_delivery_days' => 'decimal:2',
        'fulfillment_rate' => 'decimal:2',
        'total_orders' => 'integer',
        'active' => 'boolean',
        'last_order_date' => 'datetime',
        'last_delivery_date' => 'datetime'
    ];

    // Relaciones

    /**
     * Productos de este proveedor (relación uno a muchos)
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Productos donde es proveedor preferido
     */
    public function preferredProducts()
    {
        return $this->allProducts()->wherePivot('is_preferred', true);
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
}
