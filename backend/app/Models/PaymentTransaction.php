<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'tenant_id',
        'preference_id',
        'payment_id',
        'plan',
        'frequency',
        'amount',
        'include_dian',
        'status',
        'status_detail',
        'metadata',
        'payment_response'
    ];

    protected $casts = [
        'include_dian' => 'boolean',
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'payment_response' => 'array'
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'tenant_id');
    }
}
