<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BiometricProfile;
use App\Models\AttendanceLog;
use App\Models\CashSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BiometricController extends Controller
{
    /**
     * Enrolar perfil biométrico: guarda imagen base + descriptor facial
     */
    public function enroll(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'     => 'required|exists:users,id',
            'image'       => 'required|string', // Base64 de la imagen
            'descriptors' => 'required|array',
            'descriptors.*' => 'numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $userId = $request->user_id;
            $newDescriptors = $request->descriptors;

            // Duplicate face check: compare against all active profiles of OTHER users
            $existingProfiles = BiometricProfile::where('active', true)
                ->where('user_id', '!=', $userId)
                ->with('user:id,name,cc')
                ->get();

            foreach ($existingProfiles as $profile) {
                $existing = $profile->descriptors_json;
                if (!is_array($existing) || count($existing) !== count($newDescriptors)) continue;

                // Euclidean distance
                $sum = 0;
                for ($i = 0; $i < count($newDescriptors); $i++) {
                    $diff = ($newDescriptors[$i] ?? 0) - ($existing[$i] ?? 0);
                    $sum += $diff * $diff;
                }
                $distance = sqrt($sum);

                if ($distance < 0.4) {
                    $userName = $profile->user->name ?? 'Desconocido';
                    $userCC = $profile->user->cc ?? 'N/A';
                    return response()->json([
                        'success' => false,
                        'message' => "Este rostro ya está registrado como \"{$userName}\" (CC: {$userCC}). No se puede enrolar la misma cara bajo otro usuario.",
                    ], 409);
                }
            }

            // Desactivar perfiles anteriores del mismo usuario
            BiometricProfile::where('user_id', $userId)->update(['active' => false]);

            // Guardar imagen base en storage privado
            $imagePath = null;
            if ($request->image) {
                $imageData = $this->decodeBase64Image($request->image);
                if ($imageData) {
                    $filename = 'biometric/' . $userId . '_' . time() . '.' . $imageData['extension'];
                    Storage::disk('local')->put($filename, $imageData['data']);
                    $imagePath = $filename;
                }
            }

            $profile = BiometricProfile::create([
                'user_id'          => $userId,
                'base_image_path'  => $imagePath,
                'descriptors_json' => $request->descriptors,
                'active'           => true,
                'enrolled_at'      => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Perfil biométrico registrado exitosamente',
                'data'    => [
                    'id'          => $profile->id,
                    'user_id'     => $profile->user_id,
                    'enrolled_at' => $profile->enrolled_at,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar perfil biométrico',
                'error'   => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Obtener el descriptor facial activo de un usuario
     */
    public function getDescriptor(int $userId)
    {
        try {
            $profile = BiometricProfile::where('user_id', $userId)
                ->active()
                ->latest('enrolled_at')
                ->first();

            if (!$profile) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró perfil biométrico activo',
                    'enrolled' => false,
                ]);
            }

            return response()->json([
                'success'     => true,
                'enrolled'    => true,
                'data'        => [
                    'user_id'     => $profile->user_id,
                    'descriptors' => $profile->descriptors_json,
                    'enrolled_at' => $profile->enrolled_at,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener descriptor biométrico',
                'error'   => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Verificar si un usuario tiene perfil biométrico activo
     */
    public function checkEnrollment(int $userId)
    {
        $profile = BiometricProfile::where('user_id', $userId)
            ->active()
            ->exists();

        return response()->json([
            'success'  => true,
            'enrolled' => $profile,
        ]);
    }

    /**
     * Registrar punteo de asistencia (entrada/salida)
     */
    public function recordAttendance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'            => 'required|exists:users,id',
            'event_type'         => 'required|in:entry,exit,break_start,break_end',
            'verification_score' => 'required|numeric|min:0|max:1',
            'image'              => 'nullable|string', // Base64 de la captura de auditoría
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            // Verificar umbral de seguridad (distancia < 0.4 = match válido)
            if ($request->verification_score >= 0.4) {
                return response()->json([
                    'success' => false,
                    'message' => 'La verificación facial no superó el umbral de seguridad. Intente de nuevo posicionándose correctamente frente a la cámara.',
                ], 403);
            }

            // Verificar que el usuario tiene perfil biométrico
            $hasProfile = BiometricProfile::where('user_id', $request->user_id)
                ->active()
                ->exists();

            if (!$hasProfile) {
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario no tiene un perfil biométrico activo. Debe enrolarse primero.',
                ], 422);
            }

            // Prevenir punteos duplicados en los últimos 5 minutos
            $recentLog = AttendanceLog::where('user_id', $request->user_id)
                ->where('event_type', $request->event_type)
                ->where('event_at', '>=', now()->subMinutes(5))
                ->exists();

            if ($recentLog) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya se registró este evento hace menos de 5 minutos.',
                ], 429);
            }

            // Validar secuencia lógica del turno
            $todayLogs = AttendanceLog::where('user_id', $request->user_id)
                ->whereDate('event_at', today())
                ->pluck('event_type')
                ->toArray();

            $hasEntry = in_array('entry', $todayLogs);
            $hasExit = in_array('exit', $todayLogs);
            $breakStarts = count(array_filter($todayLogs, fn($t) => $t === 'break_start'));
            $breakEnds = count(array_filter($todayLogs, fn($t) => $t === 'break_end'));
            $onBreak = $breakStarts > $breakEnds;

            $eventType = $request->event_type;

            // Jornada completa: no permitir más punteos
            if ($hasEntry && $hasExit) {
                return response()->json([
                    'success' => false,
                    'message' => 'La jornada ya fue completada hoy (entrada y salida registradas).',
                ], 422);
            }

            // Validaciones por tipo de evento
            if ($eventType === 'entry' && $hasEntry) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya se registró la entrada hoy. No puede iniciar otra jornada.',
                ], 422);
            }

            if (in_array($eventType, ['exit', 'break_start', 'break_end']) && !$hasEntry) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debe registrar la entrada primero antes de esta acción.',
                ], 422);
            }

            if ($eventType === 'break_start' && $onBreak) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya hay un break en curso. Debe finalizarlo primero.',
                ], 422);
            }

            if ($eventType === 'break_end' && !$onBreak) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay un break activo para finalizar.',
                ], 422);
            }

            if ($eventType === 'exit' && $onBreak) {
                // Auto-cerrar break abierto al registrar salida
                AttendanceLog::create([
                    'user_id'             => $request->user_id,
                    'event_type'          => 'break_end',
                    'event_at'            => now(),
                    'verification_score'  => $request->verification_score,
                    'ip_address'          => $request->ip(),
                    'user_agent'          => $request->userAgent(),
                ]);
            }

            $log = AttendanceLog::create([
                'user_id'             => $request->user_id,
                'event_type'          => $request->event_type,
                'event_at'            => now(),
                'verification_score'  => $request->verification_score,
                'ip_address'          => $request->ip(),
                'user_agent'          => $request->userAgent(),
            ]);

            $eventLabels = [
                'entry'       => 'Entrada',
                'exit'        => 'Salida',
                'break_start' => 'Inicio de break',
                'break_end'   => 'Fin de break',
            ];
            $eventLabel = $eventLabels[$request->event_type] ?? $request->event_type;

            return response()->json([
                'success' => true,
                'message' => $eventLabel . ' registrada exitosamente a las ' . now()->format('h:i A'),
                'data'    => [
                    'id'         => $log->id,
                    'event_type' => $log->event_type,
                    'event_at'   => $log->event_at,
                    'score'      => $log->verification_score,
                ],
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Error en recordAttendance', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => $request->user_id,
                'event_type' => $request->event_type,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar asistencia: ' . $e->getMessage(),
                'error'   => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Obtener historial de asistencia de un usuario (o todos si es admin)
     */
    public function attendanceHistory(Request $request)
    {
        try {
            $query = AttendanceLog::with('user:id,name,email,cc')
                ->orderBy('event_at', 'desc');

            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            if ($request->has('date')) {
                $query->whereDate('event_at', $request->date);
            } else {
                $query->whereDate('event_at', today());
            }

            $logs = $query->get()->map(fn($log) => [
                'id'                 => $log->id,
                'user_id'            => $log->user_id,
                'user_name'          => $log->user?->name ?? 'Desconocido',
                'user_cc'            => $log->user?->cc,
                'event_type'         => $log->event_type,
                'event_at'           => $log->event_at,
                'verification_score' => (float) $log->verification_score,
                'closed_by'          => $log->closed_by ?? 'user',
                'is_auto_closed'     => (bool) $log->is_auto_closed,
            ]);

            return response()->json([
                'success' => true,
                'data'    => $logs,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener historial de asistencia',
                'error'   => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Resumen de asistencia del día actual (para KPI)
     */
    public function todaySummary()
    {
        try {
            $today = Carbon::today();

            $entries = AttendanceLog::whereDate('event_at', $today)
                ->where('event_type', 'entry')
                ->with('user:id,name')
                ->get();

            $exits = AttendanceLog::whereDate('event_at', $today)
                ->where('event_type', 'exit')
                ->with('user:id,name')
                ->get();

            $enrolledCount = BiometricProfile::where('active', true)->count();
            $totalUsers = User::where('active', true)->count();

            return response()->json([
                'success' => true,
                'data'    => [
                    'entries_today'    => $entries->count(),
                    'exits_today'      => $exits->count(),
                    'enrolled_users'   => $enrolledCount,
                    'total_users'      => $totalUsers,
                    'pending_enroll'   => $totalUsers - $enrolledCount,
                    'latest_entries'   => $entries->take(5)->map(fn($e) => [
                        'user_name' => $e->user?->name,
                        'time'      => $e->event_at?->format('h:i A'),
                        'score'     => (float) $e->verification_score,
                    ]),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener resumen del día',
                'error'   => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Eliminar perfil biométrico de un usuario (admin)
     */
    public function deleteProfile(int $userId)
    {
        try {
            $deleted = BiometricProfile::where('user_id', $userId)->delete();

            return response()->json([
                'success' => true,
                'message' => $deleted
                    ? 'Perfil biométrico eliminado'
                    : 'No se encontró perfil biométrico',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar perfil biométrico',
                'error'   => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Check if user can end their shift (must close cash register first)
     */
    public function checkCashBeforeExit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'user_id es requerido',
            ], 422);
        }

        try {
            $userId = $request->user_id;

            $openSession = CashSession::where('user_id', $userId)
                ->where('status', 'open')
                ->with('warehouse:id,name')
                ->first();

            if ($openSession) {
                return response()->json([
                    'success' => true,
                    'can_exit' => true,
                    'has_open_cash' => true,
                    'warehouse_name' => $openSession->warehouse?->name ?? 'Principal',
                    'opened_at' => $openSession->opened_at,
                    'message' => 'Tienes una caja abierta en la sede ' . ($openSession->warehouse?->name ?? 'Principal') . '. Se recomienda realizar el cierre de caja antes de finalizar la jornada.',
                ]);
            }

            return response()->json([
                'success' => true,
                'can_exit' => true,
                'has_open_cash' => false,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al verificar estado de caja',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Obtener todos los descriptores de perfiles biométricos activos
     * para identificación 1:N en el frontend
     */
    public function getAllDescriptors()
    {
        try {
            $profiles = BiometricProfile::where('active', true)
                ->with('user:id,name,cc')
                ->get(['id', 'user_id', 'descriptors_json']);

            $data = $profiles->map(function ($profile) {
                return [
                    'user_id'     => $profile->user_id,
                    'name'        => $profile->user->name ?? 'Desconocido',
                    'cc'          => $profile->user->cc ?? '',
                    'descriptors' => $profile->descriptors_json,
                ];
            });

            return response()->json([
                'success' => true,
                'data'    => $data,
                'count'   => $data->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener descriptores',
                'error'   => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Buscar usuario por cédula (CC) y retornar estado de enrolamiento
     */
    public function lookupUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cc' => 'required|string|min:3',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Debe proporcionar un número de cédula válido',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $user = User::with('role:id,name')
                ->where('cc', $request->cc)
                ->where('active', true)
                ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró un empleado activo con la cédula proporcionada',
                    'found'   => false,
                ]);
            }

            $profile = BiometricProfile::where('user_id', $user->id)
                ->active()
                ->latest('enrolled_at')
                ->first();

            return response()->json([
                'success' => true,
                'found'   => true,
                'data'    => [
                    'id'          => $user->id,
                    'name'        => $user->name,
                    'cc'          => $user->cc,
                    'email'       => $user->email,
                    'role'        => $user->role?->name ?? 'Sin rol',
                    'enrolled'    => !!$profile,
                    'enrolled_at' => $profile?->enrolled_at,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar el usuario',
                'error'   => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Actualizar un registro de asistencia (admin)
     */
    public function updateAttendanceLog(Request $request, int $id)
    {
        $validator = Validator::make($request->all(), [
            'event_at' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $log = AttendanceLog::findOrFail($id);
            $log->event_at = Carbon::parse($request->event_at);
            $log->save();

            return response()->json([
                'success' => true,
                'message' => 'Registro de asistencia actualizado',
                'data'    => [
                    'id'         => $log->id,
                    'user_id'    => $log->user_id,
                    'event_type' => $log->event_type,
                    'event_at'   => $log->event_at,
                ],
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registro no encontrado',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar registro',
                'error'   => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Eliminar un registro de asistencia (admin)
     */
    public function deleteAttendanceLog(int $id)
    {
        try {
            $log = AttendanceLog::findOrFail($id);
            $userName = $log->user?->name ?? 'Desconocido';
            $eventType = $log->event_type;
            $log->delete();

            return response()->json([
                'success' => true,
                'message' => "Registro de {$eventType} de {$userName} eliminado",
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registro no encontrado',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar registro',
                'error'   => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Eliminar todos los registros de asistencia de un usuario en una fecha (admin)
     */
    public function deleteUserAttendanceLogs(Request $request, int $userId)
    {
        try {
            $date = $request->input('date', today()->toDateString());

            $deleted = AttendanceLog::where('user_id', $userId)
                ->whereDate('event_at', $date)
                ->delete();

            $user = User::find($userId);
            $userName = $user?->name ?? 'Desconocido';

            return response()->json([
                'success' => true,
                'message' => "Se eliminaron {$deleted} registros de {$userName} del {$date}",
                'deleted_count' => $deleted,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar registros',
                'error'   => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Decodificar imagen Base64
     */
    private function decodeBase64Image(string $base64): ?array
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $base64, $matches)) {
            $extension = $matches[1];
            $data = base64_decode(substr($base64, strpos($base64, ',') + 1));
            if ($data !== false) {
                return ['data' => $data, 'extension' => $extension];
            }
        }

        // Sin prefijo data URI, intentar como base64 puro
        $data = base64_decode($base64);
        if ($data !== false) {
            return ['data' => $data, 'extension' => 'jpg'];
        }

        return null;
    }
}
