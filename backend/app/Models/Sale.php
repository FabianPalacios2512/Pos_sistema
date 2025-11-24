<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'user_id',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'payment_method',
        'cash_received',
        'change_given',
        'status',
        'notes',
        'sale_date'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'cash_received' => 'decimal:2',
        'change_given' => 'decimal:2',
        'sale_date' => 'datetime'
    ];

    // Relaciones
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('sale_date', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('sale_date', now()->month)
                    ->whereYear('sale_date', now()->year);
    }

    // Métodos auxiliares
    public function generateInvoiceNumber()
    {
        $prefix = 'INV';
        $date = now()->format('Ymd');
        $sequence = Sale::whereDate('created_at', today())->count() + 1;

        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function calculateTotals()
    {
        $this->subtotal = $this->saleItems->sum('total_price');
        $this->total_amount = $this->subtotal + $this->tax_amount - $this->discount_amount;
        $this->save();
    }

    public function getItemsCountAttribute()
    {
        return $this->saleItems->sum('quantity');
    }

    public function getTotalProfitAttribute()
    {
        return $this->saleItems->sum(function ($item) {
            $costPrice = $item->product->cost_price ?? 0;
            return ($item->unit_price - $costPrice) * $item->quantity;
        });
    }
}
