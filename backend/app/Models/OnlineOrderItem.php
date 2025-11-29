<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlineOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'online_order_id',
        'product_id',
        'product_name',
        'product_sku',
        'quantity',
        'unit_price',
        'subtotal',
        'special_instructions',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            if ($item->product_id && empty($item->product_name)) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $item->product_name = $product->name;
                    $item->product_sku = $product->sku;
                    $item->unit_price = $product->sale_price;
                }
            }

            $item->subtotal = $item->quantity * $item->unit_price;
        });
    }

    /**
     * Relación con el pedido
     */
    public function order()
    {
        return $this->belongsTo(OnlineOrder::class, 'online_order_id');
    }

    /**
     * Relación con el producto
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Recalcula el subtotal
     */
    public function recalculateSubtotal(): void
    {
        $this->subtotal = $this->quantity * $this->unit_price;
        $this->save();
    }
}
