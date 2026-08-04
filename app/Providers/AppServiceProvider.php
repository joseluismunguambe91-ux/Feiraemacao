<?php

namespace App\Providers;

use App\Models\Feira;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        // Regra transversal (RC02/RC05, Etapa 4): nada se edita numa feira arquivada.
        Gate::define('feira-editavel', function (User $user, ?Feira $feira) {
            return $feira !== null && $feira->estado !== 'arquivada';
        });
    }
}
