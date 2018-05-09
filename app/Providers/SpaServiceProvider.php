<?php

namespace App\Providers;

use App\Spa\UrlGenerator;
use Illuminate\Support\ServiceProvider;
use Psr\Container\ContainerInterface;

final class SpaServiceProvider extends ServiceProvider
{
    protected $defer = true;

    public function register(): void
    {
        $this->app->bind(UrlGenerator::class, function (ContainerInterface $container): UrlGenerator {
            return new UrlGenerator(env('SPA_URL'));
        });
    }

    public function provides()
    {
        return [UrlGenerator::class];
    }
}
