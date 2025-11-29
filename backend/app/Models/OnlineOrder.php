<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OnlineOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'order_number',
        'customer_name',
        'customer_phone',
        'customer_document',
        'customer_address',
        'delivery_type',
        'status',
        'subtotal',
        'tax',
        'delivery_fee',
        'total',
        'note',
        'confirmed_at',
        'completed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->uuid)) {
                $order->uuid = Str::uuid();
            }

            if (empty($order->order_number)) {
                $order->order_number = self::generateOrderNumber();
            }
        });
    }

    /**
     * Genera un número de orden único y amigable
     */
    public static function generateOrderNumber(): string
    {
        $prefix = 'PED';
        $lastOrder = self::latest('id')->first();
        $number = $lastOrder ? ($lastOrder->id + 1) : 1;

        return sprintf('%s-%03d', $prefix, $number);
    }

    /**
     * Relación con los items del pedido
     */
    public function items()
    {
        return $this->hasMany(OnlineOrderItem::class);
    }

    /**
     * Calcula el total del pedido basado en los items
     */
    public function calculateTotal(): void
    {
        $this->subtotal = $this->items->sum('subtotal');
        $this->total = $this->subtotal + $this->tax + $this->delivery_fee;
    }

    /**
     * Confirma el pedido
     */
    public function confirm(): void
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }

    /**
     * Completa el pedido
     */
    public function complete(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Cancela el pedido
     */
    public function cancel(): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
    }

    /**
     * Genera el mensaje de WhatsApp para el pedido
     */
    public function generateWhatsAppMessage(): string
    {
        $message = "🛒 *Nuevo Pedido #{$this->order_number}*\n\n";
        $message .= "👤 *Cliente:* {$this->customer_name}\n";
        $message .= "📱 *Teléfono:* {$this->customer_phone}\n";
        $message .= "📦 *Tipo:* " . ($this->delivery_type === 'delivery' ? 'Domicilio' : 'Recoger en tienda') . "\n";

        if ($this->delivery_type === 'delivery' && $this->customer_address) {
            $message .= "📍 *Dirección:* {$this->customer_address}\n";
        }

        $message .= "\n*Productos:*\n";
        foreach ($this->items as $item) {
            $message .= "• {$item->quantity}x {$item->product_name} - $" . number_format($item->subtotal, 0, ',', '.') . "\n";
        }

        $message .= "\n💰 *Total:* $" . number_format($this->total, 0, ',', '.') . "\n";

        if ($this->note) {
            $message .= "\n📝 *Nota:* {$this->note}\n";
        }

        return $message;
    }

    /**
     * Genera el enlace de WhatsApp
     */
    public function getWhatsAppLink(string $businessPhone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $businessPhone);
        $message = urlencode($this->generateWhatsAppMessage());

        return "https://wa.me/{$phone}?text={$message}";
    }

    /**
     * Scope para pedidos pendientes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope para pedidos confirmados
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope para pedidos completados
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
