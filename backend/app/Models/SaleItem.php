<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
        'discount_amount'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'discount_amount' => 'decimal:2'
    ];

    // Relaciones
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Métodos auxiliares
    public function calculateTotal()
    {
        $this->total_price = ($this->unit_price * $this->quantity) - $this->discount_amount;
        return $this;
    }

    public function getProfitAttribute()
    {
        $costPrice = $this->product->cost_price ?? 0;
        return ($this->unit_price - $costPrice) * $this->quantity;
    }

    public function getProfitMarginAttribute()
    {
        if ($this->unit_price > 0) {
            $costPrice = $this->product->cost_price ?? 0;
            return (($this->unit_price - $costPrice) / $this->unit_price) * 100;
        }
        return 0;
    }

    // Boot method para calcular total automáticamente
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($saleItem) {
            $saleItem->calculateTotal();
        });

        static::saved(function ($saleItem) {
            // Actualizar stock del producto si la venta está completada
            if ($saleItem->sale->status === 'completed' && $saleItem->product->manage_stock) {
                $saleItem->product->updateStock(
                    $saleItem->quantity,
                    'sale',
                    $saleItem->sale->invoice_number,
                    $saleItem->sale->user_id
                );
            }
        });
    }
}
