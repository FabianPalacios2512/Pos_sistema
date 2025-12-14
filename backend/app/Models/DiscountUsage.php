<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscountUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'discount_id',
        'user_identifier',
        'customer_email',
        'customer_phone',
        'invoice_number',
        'discount_amount',
        'usage_metadata',
        'used_at'
    ];

    protected $casts = [
        'usage_metadata' => 'array',
        'used_at' => 'datetime',
        'discount_amount' => 'decimal:2'
    ];

    /**
     * Relación con el descuento
     */
    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }
}
