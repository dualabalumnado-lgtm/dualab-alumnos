<?php

namespace App\Providers;

use App\Services\ClaudeAIService;
use App\Services\FichajeService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Registrar servicios como singletons
        $this->app->singleton(ClaudeAIService::class);
        $this->app->singleton(FichajeService::class);
    }

    public function boot(): void
    {
        //
    }
}
