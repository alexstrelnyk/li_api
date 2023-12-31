<?php
declare(strict_types=1);

namespace App\Providers;

use App\Models\ContentItem;
use App\Models\Focus;
use App\Models\FocusArea;
use App\Models\FocusAreaTopic;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    protected $modelsBinding = [
        'focus' => Focus::class,
        'topic' => Topic::class,
        'user' => User::class,
        'admin' => User::class,
        'contentItem' => ContentItem::class,
        'focusArea' => FocusArea::class,
        'topicArea' => FocusAreaTopic::class,
    ];

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot(): void
    {
        foreach ($this->modelsBinding as $key => $class) {
            Route::bind($key, static function ($value) use ($class) {
                return $class::find((int) $value) ?? abort(404, class_basename($class . ' not found'));
            });
        }

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map(): void
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes(): void
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes(): void
    {
        Route::prefix('api')
             ->middleware(['api'])
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }
}
