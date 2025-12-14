<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceDiscount extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'discount_id',
        'discount_code',
        'discount_name',
        'discount_type',
        'discount_value',
        'applied_amount',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'applied_amount' => 'decimal:2',
    ];

    /**
     * Relación con factura
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Relación con descuento
     */
    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }
}
