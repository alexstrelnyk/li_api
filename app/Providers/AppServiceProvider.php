<?php
declare(strict_types=1);

namespace App\Providers;

use App\Services\Api\Auth\AuthService;
use App\Services\MagicLinkTokenGenerator\BaseMagicLinkTokenGeneratorService;
use App\Services\MagicLinkTokenGenerator\MagicLinkTokenGeneratorInterface;
use App\Services\ConsecutiveDaysService\ConsecutiveDaysService;
use App\Services\ConsecutiveDaysService\ConsecutiveDaysServiceInterface;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    public $bindings = [
        MagicLinkTokenGeneratorInterface::class => BaseMagicLinkTokenGeneratorService::class,
        ConsecutiveDaysServiceInterface::class => ConsecutiveDaysService::class
    ];

    /**
     * @var array
     */
    public $singletons = [

    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
//        if ($this->app->environment() !== 'production') {
//            $this->app->register(IdeHelperServiceProvider::class);
//        }

        $this->app->singleton(AuthService::class); // TODO need clearing

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
