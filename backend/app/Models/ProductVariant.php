<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'barcode',
        'price',
        'cost_price',
        'stock',
        'options_summary',
        'active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'stock' => 'integer',
        'options_summary' => 'array',
        'active' => 'boolean'
    ];

    /**
     * Producto padre
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Stock en bodegas para esta variante específica
     */
    public function warehouses()
    {
        return $this->belongsToMany(Warehouse::class, 'product_warehouse')
            ->withPivot('stock')
            ->withTimestamps();
    }

    /**
     * Valores de opciones asociados a esta variante (ej: Talla: S, Color: Rojo)
     */
    public function optionValues()
    {
        return $this->belongsToMany(ProductOptionValue::class, 'product_variant_option_value');
    }
}
