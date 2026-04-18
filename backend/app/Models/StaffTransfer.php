<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffTransfer extends Model
{
    protected $fillable = [
        'user_id',
        'from_warehouse_id',
        'to_warehouse_id',
        'transferred_by',
        'reason',
        'closed_session_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fromWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    public function toWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    public function transferredBy()
    {
        return $this->belongsTo(User::class, 'transferred_by');
    }

    public function closedSession()
    {
        return $this->belongsTo(CashSession::class, 'closed_session_id');
    }
}
