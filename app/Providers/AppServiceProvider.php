<?php

namespace App\Providers;

use App\Services\ModuleService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ModuleService::class);
    }

    public function boot(): void
    {
        //
    }
}

if (!function_exists('module')) {
    function module(string $name): bool
    {
        return app(ModuleService::class)->isActive($name);
    }
}
