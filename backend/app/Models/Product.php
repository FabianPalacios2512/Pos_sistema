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
        'measurement_unit',    // 📏 Nueva: Unidad de medida (unit, kg, g, m, cm, l, ml)
        'allow_decimal',       // ✅ Nueva: Permite cantidades decimales
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
        'allow_decimal' => 'boolean',  // 📏 Cast para allow_decimal
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

    /**
     * Bodegas/Sedes donde está este producto
     */
    public function warehouses()
    {
        return $this->belongsToMany(Warehouse::class, 'product_warehouse')
            ->withPivot('stock')
            ->withTimestamps();
    }

    /**
     * Obtener stock total sumando todas las bodegas
     */
    public function getTotalStockAttribute()
    {
        return $this->warehouses()->sum('product_warehouse.stock');
    }

    /**
     * Obtener stock de una bodega específica
     */
    public function getStockInWarehouse($warehouseId)
    {
        $warehouse = $this->warehouses()->where('warehouse_id', $warehouseId)->first();
        return $warehouse ? $warehouse->pivot->stock : 0;
    }

    /**
     * Obtener desglose de stock por bodega
     */
    public function getStockBreakdown()
    {
        return $this->warehouses()->get()->map(function ($warehouse) {
            return [
                'warehouse_id' => $warehouse->id,
                'warehouse_name' => $warehouse->name,
                'stock' => $warehouse->pivot->stock,
            ];
        });
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

    // ==========================================
    // 📏 MÉTODOS PARA UNIDADES DE MEDIDA (UOM)
    // ==========================================

    /**
     * Obtiene la abreviatura de la unidad de medida para mostrar en UI
     *
     * @return string
     */
    public function getUnitAbbreviationAttribute(): string
    {
        return match($this->measurement_unit ?? 'unit') {
            'unit' => 'und',
            'kg' => 'kg',
            'g' => 'g',
            'm' => 'm',
            'cm' => 'cm',
            'l' => 'L',
            'ml' => 'ml',
            default => 'und'
        };
    }

    /**
     * Obtiene el nombre completo de la unidad de medida (para formularios)
     *
     * @return string
     */
    public function getUnitNameAttribute(): string
    {
        return match($this->measurement_unit ?? 'unit') {
            'unit' => 'Unidades',
            'kg' => 'Kilogramos',
            'g' => 'Gramos',
            'm' => 'Metros',
            'cm' => 'Centímetros',
            'l' => 'Litros',
            'ml' => 'Mililitros',
            default => 'Unidades'
        };
    }

    /**
     * Valida si una cantidad es válida para este producto
     *
     * @param float $quantity
     * @return bool
     */
    public function isValidQuantity(float $quantity): bool
    {
        // Si no permite decimales, la cantidad debe ser un entero
        if (!$this->allow_decimal) {
            return $quantity == floor($quantity);
        }

        // Si permite decimales, cualquier número positivo es válido
        return $quantity > 0;
    }

    /**
     * Calcula el precio total para una cantidad dada
     * Usa precisión de 2 decimales para evitar errores de redondeo
     *
     * @param float $quantity
     * @return float
     */
    public function calculateTotal(float $quantity): float
    {
        // Convertir a centavos (enteros) para evitar errores de punto flotante
        $priceInCents = (int) round($this->sale_price * 100);
        $quantityInCents = (int) round($quantity * 100);

        // Multiplicar en enteros
        $totalInCents = ($priceInCents * $quantityInCents) / 100;

        // Convertir de vuelta a decimales con 2 lugares
        return round($totalInCents / 100, 2);
    }

    /**
     * Formatea la cantidad con los decimales apropiados
     *
     * @param float $quantity
     * @return string
     */
    public function formatQuantity(float $quantity): string
    {
        if (!$this->allow_decimal) {
            return number_format($quantity, 0);
        }

        // Para productos a granel, mostrar hasta 3 decimales si es necesario
        return rtrim(rtrim(number_format($quantity, 3, '.', ''), '0'), '.');
    }

    /**
     * Obtiene el paso (step) para inputs numéricos en el frontend
     *
     * @return string
     */
    public function getQuantityStepAttribute(): string
    {
        return $this->allow_decimal ? '0.001' : '1';
    }

    /**
     * Scope para filtrar productos por unidad de medida
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|array $units
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByMeasurementUnit($query, $units)
    {
        if (is_array($units)) {
            return $query->whereIn('measurement_unit', $units);
        }

        return $query->where('measurement_unit', $units);
    }

    /**
     * Scope para productos que permiten decimales
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAllowsDecimals($query)
    {
        return $query->where('allow_decimal', true);
    }
}
