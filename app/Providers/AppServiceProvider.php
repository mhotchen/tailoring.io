<?php
namespace App\Providers;

use App\Measurement\Settings\DefaultMeasurementSettings;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        //
    }

    public function register(): void
    {
        $this->app->singleton(DefaultMeasurementSettings::class, DefaultMeasurementSettings::class);
    }
}
