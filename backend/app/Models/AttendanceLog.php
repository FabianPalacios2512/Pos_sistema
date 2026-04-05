<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_type',
        'event_at',
        'captured_image_path',
        'verification_score',
        'ip_address',
        'user_agent',
        'closed_by',
        'is_auto_closed',
        'auto_close_note',
    ];

    protected function casts(): array
    {
        return [
            'event_at' => 'datetime',
            'verification_score' => 'decimal:4',
            'is_auto_closed' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeEntries($query)
    {
        return $query->where('event_type', 'entry');
    }

    public function scopeExits($query)
    {
        return $query->where('event_type', 'exit');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('event_at', today());
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
