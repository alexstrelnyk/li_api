<?php
declare(strict_types=1);

namespace App\Providers;

use App\Services\SilScoreService\SilScoreService;
use Illuminate\Support\ServiceProvider;

class SilScoreServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(SilScoreService::class);
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
