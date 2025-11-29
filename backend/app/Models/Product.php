<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\AlertService;

class Product extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        // Disparar actualización de alertas cuando cambie el current_stock
        static::updating(function ($product) {
            if ($product->isDirty('current_stock')) {
                // Ejecutar después de que se guarde el cambio
                static::updated(function ($product) {
                    AlertService::updateProductAlerts($product->id);
                });
            }
        });

        // También cuando se crea un nuevo producto
        static::created(function ($product) {
            AlertService::updateProductAlerts($product->id);
        });
    }

    protected $fillable = [
        'name',
        'description',
        'sku',
        'barcode',
        'category_id',
        'supplier_id',
        'cost_price',
        'sale_price',
        'wholesale_price',
        'current_stock',
        'min_stock',
        'max_stock',
        'unit',
        'manage_stock',
        'active',
        'image_url',
        'tags',
        'is_public',
        'public_description',
        'public_image'
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
        'current_stock' => 'integer',
        'min_stock' => 'integer',
        'max_stock' => 'integer',
        'manage_stock' => 'boolean',
        'active' => 'boolean',
        'is_public' => 'boolean',
        'tags' => 'array'
    ];

    // Relaciones
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->where('manage_stock', true)
                    ->whereRaw('current_stock <= min_stock');
    }

    public function scopeInStock($query)
    {
        return $query->where('current_stock', '>', 0);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true)
                    ->where('active', true);
    }

    public function scopeAvailableForOnline($query)
    {
        return $query->public()
                    ->where('current_stock', '>', 0);
    }

    // Métodos auxiliares
    public function isLowStock()
    {
        return $this->manage_stock && $this->current_stock <= $this->min_stock;
    }

    public function isOutOfStock()
    {
        return $this->manage_stock && $this->current_stock <= 0;
    }

    public function getProfitMarginAttribute()
    {
        if ($this->cost_price > 0) {
            return (($this->sale_price - $this->cost_price) / $this->cost_price) * 100;
        }
        return 0;
    }

    public function updateStock($quantity, $type = 'adjustment', $reference = null, $userId = null)
    {
        $previousStock = $this->current_stock;

        if ($type === 'sale') {
            $this->current_stock -= abs($quantity);
            $quantity = -abs($quantity);
        } else {
            $this->current_stock += $quantity;
        }

        $this->save();

        // Registrar movimiento de inventario
        InventoryMovement::create([
            'product_id' => $this->id,
            'type' => $type,
            'quantity' => $quantity,
            'previous_stock' => $previousStock,
            'new_stock' => $this->current_stock,
            'reference' => $reference,
            'user_id' => $userId ?? auth()->id()
        ]);

        return $this;
    }
}
