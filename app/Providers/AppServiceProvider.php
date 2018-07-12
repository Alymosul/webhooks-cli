<?php

namespace App\Providers;

use App\Services\HttpCalls\GuzzleAdapter;
use App\Services\HttpCalls\HttpCaller;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(HttpCaller::class, GuzzleAdapter::class);
    }
}
