<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'icon',
        'active',
        'requires_reference',
        'sort_order',
        'config',
    ];

    protected $casts = [
        'active' => 'boolean',
        'requires_reference' => 'boolean',
        'config' => 'array',
    ];

    /**
     * Scope para métodos activos
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope ordenado
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
