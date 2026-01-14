<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'variant_id',           // 👗 NUEVO: para productos moda con variantes
        'variant_options',      // 👗 NUEVO: resumen de opciones (talla, color, etc.)
        'quantity_ordered',
        'quantity_received',
        'unit',
        'unit_cost',
        'subtotal',
        'tax_amount',
        'total',
        'notes',
        'received'
    ];

    protected $casts = [
        'quantity_ordered' => 'decimal:2',
        'quantity_received' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'received' => 'boolean',
        'variant_options' => 'array'  // 👗 NUEVO: cast a array
    ];

    // Relaciones
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * 👗 NUEVO: Relación con la variante del producto (para tiendas de moda)
     */
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    /**
     * 👗 Helper para obtener nombre completo del producto con variante
     */
    public function getFullProductNameAttribute()
    {
        $name = $this->product->name ?? 'Producto';

        if ($this->variant_options && is_array($this->variant_options)) {
            $options = collect($this->variant_options)
                ->map(fn($opt) => "{$opt['name']}: {$opt['value']}")
                ->join(' | ');
            return "{$name} ({$options})";
        }

        return $name;
    }

    // Métodos auxiliares
    public function getPendingQuantityAttribute()
    {
        return $this->quantity_ordered - $this->quantity_received;
    }

    public function getReceivedPercentageAttribute()
    {
        return $this->quantity_ordered > 0
            ? ($this->quantity_received / $this->quantity_ordered) * 100
            : 0;
    }

    public function isFullyReceived()
    {
        return $this->quantity_received >= $this->quantity_ordered;
    }
}
