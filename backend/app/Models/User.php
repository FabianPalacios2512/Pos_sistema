<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'cc',
        'password',
        'role_id',
        'warehouse_id',
        'phone',
        'active',
        'last_login',
        'tour_completed',
        'google_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
            'last_login' => 'datetime',
            'tour_completed' => 'boolean'
        ];
    }

    // Relaciones
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(\App\Models\Warehouse::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function expenses()
    {
        return $this->hasMany(\App\Models\Expense::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    // Métodos auxiliares
    public function hasPermission($permission)
    {
        return $this->role?->hasPermission($permission) ?? false;
    }

    public function hasModulePermission($module)
    {
        return $this->role?->hasModulePermission($module) ?? false;
    }

    public function updateLastLogin()
    {
        $this->last_login = now();
        $this->save();
    }

    /**
     * Check if user is a full administrator (sees all sedes)
     */
    public function isFullAdmin(): bool
    {
        $this->loadMissing('role');
        $roleName = strtolower($this->role->name ?? '');
        return in_array($roleName, ['administrador', 'admin', 'superadmin']);
    }

    /**
     * Check if user is admin POS (admin scoped to their sede)
     */
    public function isAdminPos(): bool
    {
        $this->loadMissing('role');
        $roleName = strtolower($this->role->name ?? '');
        return $roleName === 'administrador pos';
    }

    /**
     * Check if user has any admin-level role (full admin OR admin POS)
     * Use this for permission checks (can edit, can manage, etc.)
     */
    public function isAnyAdmin(): bool
    {
        return $this->isFullAdmin() || $this->isAdminPos();
    }

    /**
     * Check if user is a vendedor/cajero (limited role)
     */
    public function isVendedor(): bool
    {
        $this->loadMissing('role');
        $roleName = strtolower($this->role->name ?? '');
        return in_array($roleName, ['vendedor', 'cajero']);
    }

    /**
     * Check if this user's data should be scoped to their warehouse
     * Full admins see all, everyone else is scoped to their warehouse_id
     */
    public function isWarehouseScoped(): bool
    {
        return !$this->isFullAdmin();
    }
}
