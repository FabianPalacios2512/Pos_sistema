<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiometricProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'base_image_path',
        'descriptors_json',
        'active',
        'enrolled_at',
    ];

    protected function casts(): array
    {
        return [
            'descriptors_json' => 'array',
            'active' => 'boolean',
            'enrolled_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
