<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReturn extends Model
{
    use HasFactory;

    protected $table = 'returns';

    protected $fillable = [
        'number',
        'original_invoice_id',
        'customer_id',
        'cash_session_id',
        'user_id',
        'return_date',
        'subtotal',
        'tax_amount',
        'total',
        'refund_method',
        'status',
        'reason',
        'notes',
        'items'
    ];

    protected $casts = [
        'return_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'items' => 'array'
    ];

    // Relaciones
    public function originalInvoice()
    {
        return $this->belongsTo(Invoice::class, 'original_invoice_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function cashSession()
    {
        return $this->belongsTo(CashSession::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function returnItems()
    {
        return $this->hasMany(ReturnItem::class, 'return_id');
    }

    /**
     * Obtener items desde el campo JSON (alternativa a returnItems)
     */
    public function getReturnItemsFromJson()
    {
        if (!$this->items) {
            return collect();
        }

        return collect($this->items);
    }

    /**
     * Accessor para obtener items con información de productos
     */
    public function getItemsWithProductsAttribute()
    {
        $items = $this->getReturnItemsFromJson();

        return $items->map(function ($item) {
            if (isset($item['product_id'])) {
                $product = Product::find($item['product_id']);
                $item['product'] = $product;
            }
            return $item;
        });
    }

    // Generar número de devolución automático
    public static function generateReturnNumber()
    {
        // Buscar el último número de devolución con formato RET-XXXXXX
        $lastReturn = static::where('number', 'LIKE', 'RET-%')
            ->orderBy('number', 'desc')
            ->first();

        if (!$lastReturn) {
            return 'RET-000001';
        }

        // Extraer el número y incrementar
        $lastNumber = intval(substr($lastReturn->number, 4));
        $nextNumber = $lastNumber + 1;

        // Verificar que el número no exista (por seguridad)
        $newNumber = 'RET-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
        while (static::where('number', $newNumber)->exists()) {
            $nextNumber++;
            $newNumber = 'RET-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
        }

        return $newNumber;
    }

    // Scopes
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('return_date', [$startDate, $endDate]);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByCashSession($query, $sessionId)
    {
        return $query->where('cash_session_id', $sessionId);
    }
}
