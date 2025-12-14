<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserNotificationView extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'last_viewed_at',
        'viewed_items'
    ];

    protected $casts = [
        'last_viewed_at' => 'datetime',
        'viewed_items' => 'array'
    ];

    // Relación con User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
