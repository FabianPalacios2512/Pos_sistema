<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'cash_session_id',
        'user_id',
        'type',
        'amount',
        'concept',
        'reference',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function cashSession(): BelongsTo
    {
        return $this->belongsTo(CashSession::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeIngresos($query)
    {
        return $query->where('type', 'ingreso');
    }

    public function scopeEgresos($query)
    {
        return $query->where('type', 'egreso');
    }

    public function scopeForSession($query, $sessionId)
    {
        return $query->where('cash_session_id', $sessionId);
    }
}
