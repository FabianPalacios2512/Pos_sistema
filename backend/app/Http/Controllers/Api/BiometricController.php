<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BiometricProfile;
use App\Models\AttendanceLog;
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
            'event_type'         => 'required|in:entry,exit',
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
                    'message' => 'Ya registraste tu ' . ($request->event_type === 'entry' ? 'entrada' : 'salida') . ' hace menos de 5 minutos.',
                ], 429);
            }

            // Guardar imagen de auditoría
            $capturedImagePath = null;
            if ($request->image) {
                $imageData = $this->decodeBase64Image($request->image);
                if ($imageData) {
                    $filename = 'attendance/' . $request->user_id . '_' . $request->event_type . '_' . time() . '.' . $imageData['extension'];
                    Storage::disk('local')->put($filename, $imageData['data']);
                    $capturedImagePath = $filename;
                }
            }

            $log = AttendanceLog::create([
                'user_id'             => $request->user_id,
                'event_type'          => $request->event_type,
                'event_at'            => now(),
                'captured_image_path' => $capturedImagePath,
                'verification_score'  => $request->verification_score,
                'ip_address'          => $request->ip(),
                'user_agent'          => $request->userAgent(),
            ]);

            $eventLabel = $request->event_type === 'entry' ? 'Entrada' : 'Salida';

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
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar asistencia',
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
