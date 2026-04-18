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
     * Usuarios asignados a esta sede
     */
    public function users()
    {
        return $this->hasMany(User::class);
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
    public function getProductStock($productId, $variantId = null)
    {
        if ($variantId) {
            $row = \DB::table('product_warehouse')
                ->where('warehouse_id', $this->id)
                ->where('product_id', $productId)
                ->where('product_variant_id', $variantId)
                ->first();
            return $row ? (int)$row->stock : 0;
        }
        $pivot = $this->products()->where('product_id', $productId)->first();
        return $pivot ? $pivot->pivot->stock : 0;
    }

    /**
     * Actualizar stock de un producto en esta bodega
     */
    public function updateProductStock($productId, $quantity, $variantId = null)
    {
        if ($variantId) {
            return \DB::table('product_warehouse')
                ->where('warehouse_id', $this->id)
                ->where('product_id', $productId)
                ->where('product_variant_id', $variantId)
                ->update([
                    'stock' => $quantity,
                    'updated_at' => now(),
                ]);
        }
        return $this->products()->updateExistingPivot($productId, [
            'stock' => $quantity,
            'updated_at' => now(),
        ]);
    }

    /**
     * Incrementar stock de un producto
     */
    public function incrementProductStock($productId, $quantity, $variantId = null)
    {
        if ($variantId) {
            $row = \DB::table('product_warehouse')
                ->where('warehouse_id', $this->id)
                ->where('product_id', $productId)
                ->where('product_variant_id', $variantId)
                ->first();

            if ($row) {
                return \DB::table('product_warehouse')
                    ->where('id', $row->id)
                    ->update([
                        'stock' => $row->stock + $quantity,
                        'updated_at' => now(),
                    ]);
            } else {
                return \DB::table('product_warehouse')->insert([
                    'warehouse_id' => $this->id,
                    'product_id' => $productId,
                    'product_variant_id' => $variantId,
                    'stock' => $quantity,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Original logic for non-variant products
        $pivot = $this->products()->where('product_id', $productId)->first();

        if ($pivot) {
            $currentStock = $pivot->pivot->stock;
            return $this->updateProductStock($productId, $currentStock + $quantity);
        } else {
            return $this->products()->attach($productId, [
                'stock' => $quantity,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Decrementar stock de un producto
     */
    public function decrementProductStock($productId, $quantity, $variantId = null)
    {
        $currentStock = $this->getProductStock($productId, $variantId);
        return $this->updateProductStock($productId, $currentStock - $quantity, $variantId);
    }
}
