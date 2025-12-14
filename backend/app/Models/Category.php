<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'icon',
        'color',
        'active',
        'products_count'
    ];

    protected $casts = [
        'active' => 'boolean',
        'products_count' => 'integer'
    ];

    // Relaciones
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    // Métodos auxiliares
    public function updateProductsCount()
    {
        $this->products_count = $this->products()->count();
        $this->save();
    }
}
