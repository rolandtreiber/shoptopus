<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
//            $files = [
//                'auth.php',
//                'admin.php',
//                'public.php'
//            ];
//
//            foreach ($files as $file) {
//                Route::middleware('api')
//                    ->prefix('api')
//
//                    ->group(base_path('routes/api/'.$file));
//            }

            foreach (File::allFiles(base_path('routes/api')) as $file) {
                Route::middleware('api')
                    ->prefix('api')
                    ->group($file->getPathname());
            }

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
