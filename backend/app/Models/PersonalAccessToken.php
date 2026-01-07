<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

/**
 * PersonalAccessToken personalizado para soportar multitenancy.
 *
 * El modelo base de Sanctum puede usar una conexión de DB fija,
 * pero en multitenancy necesitamos que use la conexión del tenant activo.
 */
class PersonalAccessToken extends SanctumPersonalAccessToken
{
    /**
     * Obtener la conexión de base de datos para el modelo.
     *
     * En contexto de tenant, usará la conexión 'tenant'.
     * Fuera del contexto de tenant, usará la conexión por defecto.
     *
     * @return string|null
     */
    public function getConnectionName()
    {
        // Si tenancy está inicializado, usar la conexión del tenant
        if (function_exists('tenancy') && tenancy()->initialized) {
            return 'tenant';
        }

        // Fallback a la conexión por defecto (central)
        return config('database.default');
    }
}
