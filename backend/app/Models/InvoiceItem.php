<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'product_id',
        'product_name',
        'product_sku',
        'quantity',
        'unit_price',
        'cost_price',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'notes'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2'
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Calculated attribute for final price
    public function getFinalPriceAttribute()
    {
        return $this->subtotal - $this->discount_amount;
    }

    // Calculated attribute for discount percentage
    public function getDiscountPercentageAttribute()
    {
        if ($this->subtotal > 0) {
            return round(($this->discount_amount / $this->subtotal) * 100, 2);
        }
        return 0;
    }
}
