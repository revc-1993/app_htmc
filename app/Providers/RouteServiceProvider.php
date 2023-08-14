<?php

namespace App\Providers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }

    protected function mapInertiaRoutes()
    {
        Route::group(
            [
                'middleware' => 'web',
                'as' => 'inertia.',
                'inertia' => 'inertia',
            ],
            function () {
                // Load the Inertia routes
                require base_path('routes/inertia.php');
            }
        );

        // Capitalize the route names
        $routeCollection = Route::getRoutes();
        foreach ($routeCollection as $route) {
            $name = $route->getName();
            if ($name && Str::startsWith($name, 'inertia.')) {
                $capitalizedName = str_replace(' ', '', ucwords(str_replace('.', ' ', $name)));
                Route::getRoutes()->getByName($name)->setName($capitalizedName);
            }
        }
    }
}
