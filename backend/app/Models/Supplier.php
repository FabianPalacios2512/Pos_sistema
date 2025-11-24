<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_name',
        'phone',
        'email',
        'address',
        'city',
        'country',
        'tax_id',
        'credit_limit',
        'current_debt',
        'active'
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'current_debt' => 'decimal:2',
        'active' => 'boolean'
    ];

    // Relaciones
    public function products()
    {
        return $this->hasMany(Product::class);
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
