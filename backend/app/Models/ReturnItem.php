<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_id',
        'product_id',
        'original_invoice_item_id',
        'quantity',
        'original_quantity',
        'unit_price',
        'discount_amount',
        'subtotal',
        'reason'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'original_quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];

    // Relaciones
    public function productReturn()
    {
        return $this->belongsTo(ProductReturn::class, 'return_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function originalInvoiceItem()
    {
        return $this->belongsTo(InvoiceItem::class, 'original_invoice_item_id');
    }

    // Métodos auxiliares
    public function calculateSubtotal()
    {
        return $this->quantity * ($this->unit_price - $this->discount_amount);
    }
}
