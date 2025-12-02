<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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
        ]);

        // Validación manual del dominio para ser más precisos
        $fullDomain = $request->subdomain . '.' . request()->getHost(); // O usar una variable de entorno para el dominio base
        // Para desarrollo local suele ser localhost, para prod sistemapos.space
        // Vamos a asumir que el usuario envía el subdominio y nosotros le pegamos el dominio base del entorno.

        // Mejor enfoque: Validar si existe algún dominio que empiece con ese subdominio en la tabla domains
        // O simplemente intentar crear el tenant y capturar el error, pero mejor validar antes.

        // Nota: Stancl Tenancy guarda el dominio completo en la tabla domains.
        // Si estamos en localhost, el dominio será subdominio.localhost

        // Detectar dominio base
        // En local, forzamos .localhost para que coincida con la configuración de Vite y Hosts
        $baseDomain = app()->environment('local') ? 'localhost' : (config('tenancy.central_domains')[0] ?? request()->getHost());
        $domainToCreate = $request->subdomain . '.' . $baseDomain;

        if (\Stancl\Tenancy\Database\Models\Domain::where('domain', $domainToCreate)->exists()) {
            return response()->json([
                'message' => 'El subdominio ya está en uso.',
                'errors' => ['subdomain' => ['Este nombre de tienda ya está en uso, prueba otro.']]
            ], 422);
        }

        // Check if tenant ID exists (to prevent 500 error on duplicate primary key)
        if (Tenant::find($request->subdomain)) {
             return response()->json([
                'message' => 'El nombre de la tienda ya está registrado.',
                'errors' => ['subdomain' => ['Este nombre de tienda ya está registrado, por favor elige otro.']]
            ], 422);
        }

        try {
            // Determinar el plan según el token (si existe)
            $plan = 'free_trial'; // Plan por defecto

            if ($request->token) {
                // Buscar el token en la base de datos
                $signupToken = \DB::connection('mysql')->table('signup_tokens')
                    ->where('token', $request->token)
                    ->where('used', false)
                    ->where('expires_at', '>', now())
                    ->first();

                if ($signupToken) {
                    $plan = $signupToken->plan;
                }
            }

            // Reemplazar guiones por guiones bajos en el ID para evitar problemas con nombres de BD
            $tenantId = str_replace('-', '_', $request->subdomain);

            // 🎯 PASO 1: Crear tenant en base de datos central
            $tenant = Tenant::create([
                'id' => $tenantId,
                'business_name' => $request->company_name,
                'plan' => $plan,
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

            // 🎯 PASO 4: Actualizar datos del tenant después de que el seeder se ejecute
            // NOTA: Los seeders se ejecutan AUTOMÁTICAMENTE por TenancyServiceProvider (Jobs\SeedDatabase)
            // Esto crea roles, usuarios admin, categorías, etc.
            // Aquí solo actualizamos los datos del usuario admin con la info del registro
            $tenant->run(function () use ($request) {
                try {
                    // El seeder ya creó un usuario admin con role_id 1
                    // Vamos a actualizarlo con los datos del registro
                    $adminUser = \App\Models\User::where('role_id', 1)->first();

                    if ($adminUser) {
                        $adminUser->update([
                            'name' => $request->owner_name,
                            'email' => $request->email,
                            'cc' => $request->cedula,
                            'password' => bcrypt($request->password),
                        ]);

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
                        'onboarding_completed' => true, // Marcar onboarding como completado
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
            // En producción sería https://subdominio.sistemapos.space

            $protocol = request()->secure() ? 'https://' : 'http://';

            // Ajuste para entorno de desarrollo vs producción
            $redirectUrl = '';
            if (app()->environment('local')) {
                // En local usamos el puerto 3000 para el frontend
                $redirectUrl = $protocol . $domainToCreate . ':3000/login';
            } else {
                $redirectUrl = $protocol . $domainToCreate . '/login';
            }

            \Log::info('✅ Tenant registrado exitosamente', [
                'tenant_id' => $tenant->id,
                'domain' => $domainToCreate,
                'redirect_url' => $redirectUrl
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tienda creada exitosamente',
                'redirect_url' => $redirectUrl,
                'tenant_id' => $tenant->id,
                'domain' => $domainToCreate
            ], 201);

        } catch (\Exception $e) {
            // 🧹 LIMPIEZA: Intentar eliminar la base de datos del tenant si se creó
            try {
                if (isset($tenant)) {
                    \Log::warning('🧹 Intentando limpiar tenant fallido', ['tenant_id' => $tenant->id]);

                    // Eliminar la base de datos del tenant si existe
                    $databaseName = 'tenant' . $tenant->id;
                    \DB::statement("DROP DATABASE IF EXISTS `{$databaseName}`");

                    // Eliminar el tenant de la tabla central (por si el rollback no funcionó)
                    Tenant::where('id', $tenant->id)->forceDelete();

                    \Log::info('✅ Tenant fallido limpiado correctamente');
                }
            } catch (\Exception $cleanupError) {
                \Log::error('⚠️ No se pudo limpiar el tenant fallido', [
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

        $baseDomain = app()->environment('local') ? 'localhost' : (config('tenancy.central_domains')[0] ?? request()->getHost());
        $domainToCheck = $request->subdomain . '.' . $baseDomain;

        $exists = \Stancl\Tenancy\Database\Models\Domain::where('domain', $domainToCheck)->exists();

        return response()->json([
            'available' => !$exists,
            'domain' => $domainToCheck
        ]);
    }
}
