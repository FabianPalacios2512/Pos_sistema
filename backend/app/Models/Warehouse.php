<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'is_default',
        'active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'active' => 'boolean',
    ];

    /**
     * Productos asociados a esta bodega/sede
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_warehouse')
            ->withPivot('stock')
            ->withTimestamps();
    }

    /**
     * Sesiones de caja de esta bodega
     */
    public function cashSessions()
    {
        return $this->hasMany(CashSession::class);
    }

    /**
     * Movimientos de inventario de esta bodega
     */
    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    /**
     * Traslados desde esta bodega
     */
    public function transfersFrom()
    {
        return $this->hasMany(StockTransfer::class, 'source_warehouse_id');
    }

    /**
     * Traslados hacia esta bodega
     */
    public function transfersTo()
    {
        return $this->hasMany(StockTransfer::class, 'destination_warehouse_id');
    }

    /**
     * Obtener stock total de un producto en esta bodega
     */
    public function getProductStock($productId)
    {
        $pivot = $this->products()->where('product_id', $productId)->first();
        return $pivot ? $pivot->pivot->stock : 0;
    }

    /**
     * Actualizar stock de un producto en esta bodega
     */
    public function updateProductStock($productId, $quantity)
    {
        return $this->products()->updateExistingPivot($productId, [
            'stock' => $quantity,
            'updated_at' => now(),
        ]);
    }

    /**
     * Incrementar stock de un producto
     */
    public function incrementProductStock($productId, $quantity)
    {
        $currentStock = $this->getProductStock($productId);
        return $this->updateProductStock($productId, $currentStock + $quantity);
    }

    /**
     * Decrementar stock de un producto
     */
    public function decrementProductStock($productId, $quantity)
    {
        $currentStock = $this->getProductStock($productId);
        return $this->updateProductStock($productId, $currentStock - $quantity);
    }
}
