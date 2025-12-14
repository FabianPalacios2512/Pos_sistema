<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConversationHistory extends Model
{
    protected $table = 'conversation_history';

    const UPDATED_AT = null; // Solo usar created_at

    protected $fillable = [
        'user_id',
        'session_id',
        'role',
        'content',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Obtener los últimos N mensajes de una sesión
     */
    public static function getRecentMessages($sessionId, $limit = 10)
    {
        return self::where('session_id', $sessionId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse() // Orden cronológico (más antiguo primero)
            ->map(function($msg) {
                return [
                    'role' => $msg->role,
                    'content' => $msg->content
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Limpiar historial antiguo (más de 7 días)
     */
    public static function cleanOldHistory()
    {
        return self::where('created_at', '<', now()->subDays(7))->delete();
    }
}

