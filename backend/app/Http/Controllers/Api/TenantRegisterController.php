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
            $tenant = Tenant::create([
                'id' => $request->subdomain, // Usamos el subdominio como ID del tenant para facilidad
                'plan' => 'free_trial',
                // Aquí podrías guardar más datos del dueño en la data del tenant si quisieras
                // o esperar a que se ejecuten los seeders/migraciones del tenant para crear el usuario en la DB del tenant
            ]);

            $tenant->domains()->create([
                'domain' => $domainToCreate
            ]);

            // Aquí es donde ocurre la magia: Crear el usuario administrador DENTRO de la base de datos del tenant
            // Stancl Tenancy permite ejecutar código en el contexto del tenant
            $tenant->run(function () use ($request) {
                // Crear rol de administrador
                $role = \App\Models\Role::create([
                    'name' => 'admin',
                    'description' => 'Administrador del Sistema',
                    'permissions' => ['ALL'], // Permisos completos (Uppercase)
                    'active' => true
                ]);

                \App\Models\User::create([
                    'name' => $request->owner_name,
                    'email' => $request->email,
                    'cc' => $request->cedula,
                    'password' => bcrypt($request->password),
                    'role_id' => $role->id,
                ]);
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

            \Log::info('Tenant registrado exitosamente', [
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
            \Log::error('Error al registrar tenant', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'subdomain' => $request->subdomain ?? 'N/A'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al crear la tienda: ' . $e->getMessage()
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
