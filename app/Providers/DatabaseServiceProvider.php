<?php
namespace App\Providers;

use App\Database\CustomConnection;
use Illuminate\Database\Connection;
use Illuminate\Database\Connectors\PostgresConnector;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

final class DatabaseServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        //
    }

    public function register(): void
    {
        $this->app->bind('db.connector.pgsql-custom', PostgresConnector::class);
        Connection::resolverFor(
            'pgsql-custom',
            function ($connection, $database, $prefix = '', array $config = []) {
                return new CustomConnection($connection, $database, $prefix, $config);
            }
        );

        Passport::ignoreMigrations();
    }
}
