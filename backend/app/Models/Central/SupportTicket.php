<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasFactory;

    /**
     * The connection name for the model.
     * Al ser un modelo central, siempre debe conectarse a 'mysql' o la base de datos principal,
     * no a la conexión 'tenant'.
     *
     * @var string
     */
    protected $connection = 'mysql';

    protected $fillable = [
        'ticket_number',
        'tenant_id',
        'user_email',
        'cc_emails',
        'user_name',
        'subject',
        'description',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Obtener el tenant asociado a este ticket (si existe)
     */
    public function tenant()
    {
        return $this->belongsTo(\App\Models\Tenant::class, 'tenant_id', 'id');
    }

    /**
     * Obtener el tenant asociado a este ticket (si existe)
     */
    public function getIsOpenAttribute()
    {
        return $this->status === 'open';
    }

    /**
     * Relación con los mensajes del caso
     */
    public function messages()
    {
        return $this->hasMany(SupportTicketMessage::class, 'support_ticket_id')->orderBy('created_at', 'asc');
    }
}
