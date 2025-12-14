<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'tenant_id',
        'plan',
        'payment_frequency',
        'amount_in_cents',
        'customer_email',
        'payment_link_id',
        'status',
    ];

    protected $casts = [
        'amount_in_cents' => 'integer',
    ];
}
