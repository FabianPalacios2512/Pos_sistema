<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActiveAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'alert_key',
        'product_id',
        'alert_type',
        'severity',
        'message',
        'metadata',
        'is_active'
    ];

    protected $casts = [
        'metadata' => 'json',
        'is_active' => 'boolean'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Crear o actualizar una alerta
     */
    public static function createOrUpdate($alertKey, $productId, $alertType, $severity, $message, $metadata = null)
    {
        return self::updateOrCreate(
            ['alert_key' => $alertKey],
            [
                'product_id' => $productId,
                'alert_type' => $alertType,
                'severity' => $severity,
                'message' => $message,
                'metadata' => $metadata,
                'is_active' => true
            ]
        );
    }

    /**
     * Desactivar una alerta
     */
    public static function deactivate($alertKey)
    {
        return self::where('alert_key', $alertKey)->update(['is_active' => false]);
    }

    /**
     * Obtener alertas activas
     */
    public static function getActive()
    {
        return self::where('is_active', true)->with('product')->get();
    }
}
