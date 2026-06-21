<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicketMessage extends Model
{
    use HasFactory;

    /**
     * The connection name for the model.
     * Al ser un modelo central, siempre debe conectarse a 'mysql'
     *
     * @var string
     */
    protected $connection = 'mysql';

    protected $fillable = [
        'support_ticket_id',
        'sender_type',
        'message',
    ];

    /**
     * Relación con el caso de soporte
     */
    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class, 'support_ticket_id');
    }
}
