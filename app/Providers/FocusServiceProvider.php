<?php

namespace App\Providers;

use App\Models\Focus;
use App\Services\Api\FocusService;
use Illuminate\Support\ServiceProvider;

class FocusServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(FocusService::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
