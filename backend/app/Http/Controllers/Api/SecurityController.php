<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SecurityService;
use Illuminate\Http\Request;

class SecurityController extends Controller
{
    private SecurityService $security;

    public function __construct()
    {
        $this->security = new SecurityService();
    }

    /**
     * GET /api/admin/security/dashboard
     * Dashboard completo de seguridad para SuperAdmin
     */
    public function dashboard()
    {
        try {
            $data = $this->security->getDashboardData();

            return response()->json([
                'success' => true,
                'data'    => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error obteniendo datos de seguridad: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/admin/security/unblock-user
     * Desbloquear una cuenta de usuario
     */
    public function unblockUser(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $result = $this->security->unblockUser($request->email);

        return response()->json([
            'success' => $result,
            'message' => $result ? 'Cuenta desbloqueada exitosamente.' : 'Error al desbloquear la cuenta.',
        ]);
    }

    /**
     * POST /api/admin/security/unblock-ip
     * Desbloquear una IP
     */
    public function unblockIp(Request $request)
    {
        $request->validate(['ip' => 'required|ip']);

        $result = $this->security->unblockIp($request->ip_address ?? $request->ip);

        return response()->json([
            'success' => $result,
            'message' => $result ? 'IP desbloqueada exitosamente.' : 'Error al desbloquear la IP.',
        ]);
    }

    /**
     * POST /api/admin/security/block-ip
     * Bloquear manualmente una IP
     */
    public function blockIp(Request $request)
    {
        $request->validate([
            'ip'        => 'required|ip',
            'permanent' => 'boolean',
        ]);

        $this->security->blockIp(
            $request->ip,
            'Bloqueado manualmente por administrador',
            $request->boolean('permanent', false)
        );

        return response()->json([
            'success' => true,
            'message' => 'IP bloqueada exitosamente.',
        ]);
    }

    /**
     * POST /api/admin/security/cleanup
     * Limpiar logs antiguos
     */
    public function cleanup()
    {
        $deleted = $this->security->cleanupOldLogs();

        return response()->json([
            'success' => true,
            'message' => "Se limpiaron {$deleted} registros antiguos.",
            'deleted' => $deleted,
        ]);
    }

    /**
     * POST /api/admin/security/resolve-event
     * Marcar evento como resuelto
     */
    public function resolveEvent(Request $request)
    {
        $request->validate(['event_id' => 'required|integer']);

        try {
            \DB::connection('mysql')->table('security_events')
                ->where('id', $request->event_id)
                ->update(['resolved' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Evento marcado como resuelto.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
