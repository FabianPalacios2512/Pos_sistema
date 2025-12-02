<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'type',
        'quantity',
        'previous_stock',
        'new_stock',
        'unit_cost',
        'reference',
        'notes',
        'reason',
        'user_id',
        'movement_date'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'previous_stock' => 'integer',
        'new_stock' => 'integer',
        'unit_cost' => 'decimal:2',
        'movement_date' => 'datetime'
    ];

    // Relaciones
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeEntries($query)
    {
        return $query->where('quantity', '>', 0);
    }

    public function scopeExits($query)
    {
        return $query->where('quantity', '<', 0);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('movement_date', today());
    }

    // Métodos auxiliares
    public function isEntry()
    {
        return $this->quantity > 0;
    }

    public function isExit()
    {
        return $this->quantity < 0;
    }

    public function getTypeDescriptionAttribute()
    {
        $descriptions = [
            'purchase' => 'Compra',
            'sale' => 'Venta',
            'adjustment' => 'Ajuste',
            'return' => 'Devolución',
            'transfer' => 'Transferencia'
        ];

        return $descriptions[$this->type] ?? $this->type;
    }

    public function getTotalValueAttribute()
    {
        return abs($this->quantity) * ($this->unit_cost ?? 0);
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($movement) {
            if (!$movement->movement_date) {
                $movement->movement_date = now();
            }
        });
    }
}
