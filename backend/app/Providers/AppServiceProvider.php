<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\ProductVariant;
use App\Observers\ProductVariantObserver;
use Laravel\Sanctum\Sanctum;
use App\Models\PersonalAccessToken;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registrar observer para sincronizar stock del producto padre
        ProductVariant::observe(ProductVariantObserver::class);

        // 🔥 CRÍTICO: Usar nuestro modelo PersonalAccessToken personalizado
        // para que Sanctum use la conexión del tenant en multitenancy
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }
}
