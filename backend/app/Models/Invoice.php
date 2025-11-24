<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'number',
        'type',
        'customer_id',
        'cash_session_id',
        'date',
        'due_date',
        'subtotal',
        'tax_amount',
        'total',
        'payment_method',
        'status',
        'notes'
    ];

    protected $casts = [
        'date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id', 'id');
    }

    // Alias para compatibilidad con el controlador de devoluciones
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id', 'id');
    }

    public function appliedDiscounts(): HasMany
    {
        return $this->hasMany(AppliedDiscount::class);
    }

    // Método para generar el siguiente número de factura
    public static function generateNextNumber($type = 'invoice')
    {
        $prefix = match($type) {
            'invoice' => 'FV',
            'quote' => 'COT',
            'credit_note' => 'NC',
            default => 'DOC'
        };

        $lastInvoice = static::where('type', $type)
            ->where('number', 'like', $prefix . '-%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastInvoice) {
            return $prefix . '-000001';
        }

        $lastNumber = (int) substr($lastInvoice->number, strlen($prefix) + 1);
        return $prefix . '-' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
    }
}
