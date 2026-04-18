<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_warehouse_id',
        'destination_warehouse_id',
        'user_id',
        'reference_number',
        'notes',
        'status',
        'transferred_at',
        'received_at',
    ];

    protected $casts = [
        'transferred_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    /**
     * Bodega origen
     */
    public function sourceWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'source_warehouse_id');
    }

    /**
     * Bodega destino
     */
    public function destinationWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'destination_warehouse_id');
    }

    /**
     * Usuario que realiza el traslado
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Items del traslado
     */
    public function items()
    {
        return $this->hasMany(StockTransferItem::class);
    }

    /**
     * Generar número de referencia automático
     */
    public static function generateReferenceNumber()
    {
        $lastTransfer = self::orderBy('id', 'desc')->first();
        $nextNumber = $lastTransfer ? $lastTransfer->id + 1 : 1;
        return 'TRF-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Completar el traslado (descontar de origen y sumar a destino)
     */
    public function complete()
    {
        if ($this->status !== 'pending') {
            throw new \Exception('Solo se pueden completar traslados pendientes');
        }

        \DB::beginTransaction();
        try {
            foreach ($this->items as $item) {
                $variantId = $item->product_variant_id;

                // Obtener stocks antes de las operaciones
                $previousStockSource = $this->sourceWarehouse->getProductStock($item->product_id, $variantId);
                $previousStockDestination = $this->destinationWarehouse->getProductStock($item->product_id, $variantId);

                // Descontar de bodega origen
                $this->sourceWarehouse->decrementProductStock($item->product_id, $item->quantity, $variantId);
                $newStockSource = $previousStockSource - $item->quantity;

                // Sumar a bodega destino
                $this->destinationWarehouse->incrementProductStock($item->product_id, $item->quantity, $variantId);
                $newStockDestination = $previousStockDestination + $item->quantity;

                // Nota descriptiva con variante si aplica
                $variantLabel = '';
                if ($variantId && $item->variant) {
                    $opts = $item->variant->options_summary;
                    if (is_array($opts)) {
                        $variantLabel = ' (' . collect($opts)->map(fn($o) => ($o['name'] ?? '') . ': ' . ($o['value'] ?? ''))->implode(', ') . ')';
                    }
                }

                // Registrar movimiento de inventario en ambas bodegas
                InventoryMovement::create([
                    'product_id' => $item->product_id,
                    'warehouse_id' => $this->source_warehouse_id,
                    'type' => 'out',
                    'reason' => 'transfer',
                    'quantity' => -$item->quantity,
                    'previous_stock' => $previousStockSource,
                    'new_stock' => $newStockSource,
                    'notes' => "Traslado {$this->reference_number} a {$this->destinationWarehouse->name}{$variantLabel}",
                    'user_id' => $this->user_id,
                    'movement_date' => now(),
                ]);

                InventoryMovement::create([
                    'product_id' => $item->product_id,
                    'warehouse_id' => $this->destination_warehouse_id,
                    'type' => 'in',
                    'reason' => 'transfer',
                    'quantity' => $item->quantity,
                    'previous_stock' => $previousStockDestination,
                    'new_stock' => $newStockDestination,
                    'notes' => "Traslado {$this->reference_number} desde {$this->sourceWarehouse->name}{$variantLabel}",
                    'user_id' => $this->user_id,
                    'movement_date' => now(),
                ]);
            }

            $this->update([
                'status' => 'completed',
                'received_at' => now(),
            ]);

            \DB::commit();
            return true;
        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }
    }
}
