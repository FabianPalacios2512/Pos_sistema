<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class TenantRegisterController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'cedula' => 'required|string|max:20',
            'subdomain' => [
                'required',
                'string',
                'alpha_dash',
                'max:50',
                Rule::unique('domains', 'domain')->where(function ($query) {
                    // Validamos contra el dominio completo (subdominio + dominio base)
                    // Pero aquí asumimos que el input es solo el subdominio
                    // La validación real de unicidad se hace mejor intentando crear o verificando manualmente
                }),
            ],
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8',
            'token' => 'nullable|string', // Token opcional para plan preseleccionado
            'plan' => 'nullable|string|in:pending,trial_express,emprendedor,negocio_pro,premium,enterprise', // Plan seleccionado (puede ser 'pending')
        ]);

        // Detectar dominio base para crear subdominios de tenants
        // En local, usamos localhost. En producción, usamos CENTRAL_DOMAIN de config (ej: 105pos.pro)
        $baseDomain = app()->environment('local')
            ? 'localhost'
            : config('app.central_domain', '105pos.pro');
        $domainToCreate = $request->subdomain . '.' . $baseDomain;

        if (\Stancl\Tenancy\Database\Models\Domain::where('domain', $domainToCreate)->exists()) {
            return response()->json([
                'message' => 'El subdominio ya está en uso.',
                'errors' => ['subdomain' => ['Este nombre de tienda ya está en uso, prueba otro.']]
            ], 422);
        }

        // Reemplazar guiones por guiones bajos en el ID para evitar problemas con nombres de BD
        $tenantId = str_replace('-', '_', $request->subdomain);

        // 🧹 LIMPIEZA PREVENTIVA: Verificar si existe una base de datos huérfana del tenant
        // Esto puede pasar si un registro anterior falló después de crear la BD
        $tenantDbName = 'tenant' . $tenantId;
        $orphanDbExists = \DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$tenantDbName]);

        if (!empty($orphanDbExists)) {
            \Log::warning("🧹 Base de datos huérfana detectada: {$tenantDbName}. Eliminando...");
            \DB::statement("DROP DATABASE IF EXISTS `{$tenantDbName}`");
            \Log::info("✅ Base de datos huérfana eliminada: {$tenantDbName}");
        }

        // Check if tenant ID exists (to prevent 500 error on duplicate primary key)
        if (Tenant::find($tenantId)) {
             return response()->json([
                'message' => 'El nombre de la tienda ya está registrado.',
                'errors' => ['subdomain' => ['Este nombre de tienda ya está registrado, por favor elige otro.']]
            ], 422);
        }

        try {
            // Determinar el plan según lo seleccionado en el formulario o el token
            $plan = $request->input('plan', 'pending'); // Por defecto pending (usuario seleccionará después)
            $subscriptionEndsAt = null;

            // Si tiene token, el token prevalece
            if ($request->token) {
                $signupToken = \DB::connection('mysql')->table('signup_tokens')
                    ->where('token', $request->token)
                    ->where('used', false)
                    ->where('expires_at', '>', now())
                    ->first();

                if ($signupToken) {
                    $plan = $signupToken->plan;
                }
            }

            // Calcular subscription_ends_at según el plan
            if ($plan === 'free_trial') {
                $subscriptionEndsAt = now()->addDays(7); // 7 días de prueba
            } elseif ($plan === 'pending') {
                // Plan pendiente: dar 7 días para que seleccione (temporal)
                $subscriptionEndsAt = now()->addDays(7);
            } elseif (in_array($plan, ['basic', 'premium', 'enterprise'])) {
                // Planes de pago: 1 mes de servicio
                $subscriptionEndsAt = now()->addMonths(1);
            }

            // 🎯 PASO 1: Crear tenant en base de datos central
            $tenant = Tenant::create([
                'id' => $tenantId,
                'business_name' => $request->company_name,
                'plan' => $plan,
                'subscription_ends_at' => $subscriptionEndsAt,
                'data' => json_encode([
                    'trial_started_at' => $plan === 'free_trial' ? now()->toDateTimeString() : null,
                    'trial_days' => $plan === 'free_trial' ? 7 : null,
                    'payment_status' => $plan === 'pending' ? 'pending_selection' : ($plan === 'free_trial' ? 'trial' : 'paid'),
                    'plan_pending' => $plan === 'pending',
                ]),
            ]);

            // 🎯 PASO 2: Crear dominio asociado
            $tenant->domains()->create([
                'domain' => $domainToCreate
            ]);

            // 🎯 PASO 3: Marcar el token como usado si existe
            if ($request->token) {
                \DB::connection('mysql')->table('signup_tokens')
                    ->where('token', $request->token)
                    ->update([
                        'used' => true,
                        'tenant_id' => $tenant->id,
                        'updated_at' => now()
                    ]);
            }

            // 🎯 PASO 3.5: Ejecutar seeder manualmente (fix para producción)
            // Los Jobs de Stancl Tenancy no ejecutan SeedDatabase correctamente en producción
            // Así que lo ejecutamos manualmente aquí para garantizar que los datos iniciales existan
            $tenant->run(function () {
                try {
                    \Log::info('🌱 Ejecutando DatabaseSeeder manualmente...');
                    $seeder = new \Database\Seeders\DatabaseSeeder();
                    $seeder->run();
                    \Log::info('✅ DatabaseSeeder ejecutado correctamente');
                } catch (\Exception $e) {
                    \Log::error('❌ Error ejecutando DatabaseSeeder: ' . $e->getMessage());
                    // No relanzamos para no abortar el registro
                }
            });

            // 🎯 PASO 4: Actualizar datos del tenant después de que el seeder se ejecute
            // NOTA: El seeder ahora se ejecuta manualmente en el PASO 3.5
            // Esto crea roles, usuarios admin, categorías, etc.
            // Aquí actualizamos los datos del usuario admin con la info del registro
            $tenant->run(function () use ($request) {
                try {
                    // El seeder ya creó un usuario admin con role_id 1
                    // Vamos a actualizarlo con los datos del registro
                    $adminUser = \App\Models\User::where('role_id', 1)->first();

                    if ($adminUser) {
                        $updateData = [
                            'name' => $request->owner_name,
                            'email' => $request->email,
                            'cc' => $request->cedula,
                        ];

                        // 🆕 Si viene de Google OAuth, usar password aleatorio y guardar google_id
                        if ($request->has('google_id') && $request->google_id) {
                            $updateData['google_id'] = $request->google_id;
                            $updateData['password'] = bcrypt(Str::random(32)); // Password aleatorio

                            \Log::info('🔐 Registro con Google OAuth detectado', [
                                'google_id' => $request->google_id,
                                'email' => $request->email
                            ]);
                        } else {
                            // Registro normal con email/password
                            $updateData['password'] = bcrypt($request->password);
                        }

                        $adminUser->update($updateData);

                        \Log::info('✅ Usuario admin actualizado con datos del registro', [
                            'name' => $request->owner_name,
                            'email' => $request->email,
                            'cc' => $request->cedula
                        ]);
                    }

                    // Actualizar system_settings con datos del registro
                    \DB::table('system_settings')->where('id', 1)->update([
                        'company_name' => $request->company_name,
                        'company_email' => $request->email,
                        'company_document' => $request->cedula, // NIT/Documento del negocio
                        'onboarding_completed' => false, // 🔥 IMPORTANTE: forzar onboarding para nuevos usuarios
                        'updated_at' => now(),
                    ]);

                    \Log::info('✅ Tenant inicializado correctamente - Datos del negocio guardados', [
                        'company_name' => $request->company_name,
                        'company_email' => $request->email,
                        'company_document' => $request->cedula
                    ]);

                } catch (\Exception $e) {
                    \Log::error('❌ Error al actualizar datos del tenant', [
                        'error' => $e->getMessage()
                    ]);
                    throw $e; // Re-lanzar para que se capture en el catch externo
                }
            });

            // Construir la URL de redirección
            // En desarrollo local con puerto 3000 para frontend, la URL sería http://subdominio.localhost:3000
            // En producción sería https://subdominio.105pos.pro

            // En producción siempre usamos HTTPS, en local depende de la request
            $protocol = app()->environment('local') ? (request()->secure() ? 'https://' : 'http://') : 'https://';

            // Ajuste para entorno de desarrollo vs producción
            $redirectUrl = '';
            if (app()->environment('local')) {
                // En local usamos el puerto 3000 para el frontend
                // ✅ Redirigir a /welcome para que pase por onboarding completo
                $redirectUrl = $protocol . $domainToCreate . ':3000/welcome';
            } else {
                // ✅ Redirigir a /welcome en producción también
                $redirectUrl = $protocol . $domainToCreate . '/welcome';
            }

            \Log::info('✅ Tenant registrado exitosamente', [
                'tenant_id' => $tenant->id,
                'domain' => $domainToCreate,
                'redirect_url' => $redirectUrl
            ]);

            // 📧 ENVIAR EMAIL DE BIENVENIDA
            try {
                \App\Services\EmailService::sendWelcomeEmail([
                    'email' => $request->email,
                    'name' => $request->owner_name,
                    'business_name' => $request->company_name,
                    'subdomain' => $request->subdomain,
                    'password' => $request->has('google_id') ? null : $request->password, // No enviar password si es OAuth
                    'plan' => $plan
                ]);
                \Log::info('📧 Email de bienvenida enviado', ['email' => $request->email]);
            } catch (\Exception $emailError) {
                // No fallar el registro si el email falla
                \Log::error('❌ Error enviando email de bienvenida', [
                    'error' => $emailError->getMessage(),
                    'email' => $request->email
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Tienda creada exitosamente',
                'redirect_url' => $redirectUrl,
                'tenant_id' => $tenant->id,
                'domain' => $domainToCreate
            ], 201);

        } catch (\Exception $e) {
            // 🧹 LIMPIEZA AGRESIVA: Eliminar TODA huella del tenant fallido
            try {
                // Usar $tenantId que se definió antes del try-catch
                if (isset($tenantId)) {
                    \Log::warning('🧹 Intentando limpiar tenant fallido', ['tenant_id' => $tenantId]);

                    // 1. Eliminar la base de datos del tenant si existe
                    $databaseName = 'tenant' . $tenantId;
                    \DB::statement("DROP DATABASE IF EXISTS `{$databaseName}`");
                    \Log::info("✅ BD eliminada: {$databaseName}");

                    // 2. Eliminar el tenant de la tabla central
                    $deletedTenant = Tenant::where('id', $tenantId)->first();
                    if ($deletedTenant) {
                        // Eliminar dominios asociados
                        $deletedTenant->domains()->delete();
                        // Eliminar tenant
                        $deletedTenant->forceDelete();
                        \Log::info("✅ Tenant eliminado de tabla central: {$tenantId}");
                    }

                    \Log::info('✅ Tenant fallido limpiado completamente');
                } elseif (isset($tenant)) {
                    // Fallback por si $tenantId no está disponible
                    $databaseName = 'tenant' . $tenant->id;
                    \DB::statement("DROP DATABASE IF EXISTS `{$databaseName}`");
                    $tenant->domains()->delete();
                    $tenant->forceDelete();
                    \Log::info('✅ Tenant fallido limpiado (usando objeto $tenant)');
                }
            } catch (\Exception $cleanupError) {
                \Log::error('⚠️ Error en limpieza de tenant fallido', [
                    'error' => $cleanupError->getMessage()
                ]);
            }

            \Log::error('❌ Error al registrar tenant', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'subdomain' => $request->subdomain ?? 'N/A'
            ]);

            // Mensaje de error más amigable
            $userMessage = 'Error al crear la tienda. Por favor, intenta nuevamente.';

            // Si es un error de CC duplicado, dar mensaje específico
            if (strpos($e->getMessage(), 'cc_unique') !== false || strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $userMessage = 'Parece que ya existe una cuenta con ese número de cédula. Si crees que es un error, contacta con soporte.';
            }

            return response()->json([
                'success' => false,
                'message' => $userMessage,
                'debug' => app()->environment('local') ? $e->getMessage() : null // Solo en desarrollo
            ], 500);
        }
    }

    public function checkDomain(Request $request)
    {
        $request->validate([
            'subdomain' => 'required|string|alpha_dash|max:50',
        ]);

        // Determinar el dominio base según el ambiente
        $centralDomain = env('CENTRAL_DOMAIN', null);

        if ($centralDomain) {
            // Si hay CENTRAL_DOMAIN configurado, usarlo (producción)
            $baseDomain = $centralDomain;
        } elseif (app()->environment('local') || in_array(request()->getHost(), ['127.0.0.1', 'localhost'])) {
            // Ambiente local
            $baseDomain = 'localhost';
        } else {
            // Fallback: usar el host actual
            $baseDomain = request()->getHost();
        }

        $domainToCheck = $request->subdomain . '.' . $baseDomain;

        $exists = \Stancl\Tenancy\Database\Models\Domain::where('domain', $domainToCheck)->exists();

        return response()->json([
            'available' => !$exists,
            'domain' => $domainToCheck
        ]);
    }
}
