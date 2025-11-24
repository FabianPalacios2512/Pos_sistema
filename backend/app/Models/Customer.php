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
        'last_purchase'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'credit_limit' => 'decimal:2',
        'current_debt' => 'decimal:2',
        'total_purchases' => 'decimal:2',
        'total_orders' => 'integer',
        'active' => 'boolean',
        'last_purchase' => 'datetime'
    ];

    // Relaciones
    public function sales()
    {
        return $this->hasMany(Sale::class);
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
}
