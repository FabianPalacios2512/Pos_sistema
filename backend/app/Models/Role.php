<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'permissions',
        'active',
        'users_count'
    ];

    protected $casts = [
        'permissions' => 'array',
        'active' => 'boolean',
        'users_count' => 'integer'
    ];

    // Relaciones
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    // Métodos auxiliares
    public function hasPermission($permission)
    {
        return in_array($permission, $this->permissions ?? []);
    }

    public function hasModulePermission($module)
    {
        $permissions = $this->permissions ?? [];
        return collect($permissions)->contains(function ($permission) use ($module) {
            return str_starts_with($permission, $module . '.');
        });
    }
}
