<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

final class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    public function map(): void
    {
        Route::middleware('api')
            ->namespace($this->namespace)
            // Ignore the warning, the PHPDoc for this method is incorrect
            ->group(base_path('routes/api.php'));
    }
}
