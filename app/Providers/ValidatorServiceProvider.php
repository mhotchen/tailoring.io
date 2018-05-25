<?php

namespace App\Providers;

use App\Http\Requests\Validators\Uuid;
use Illuminate\Support\ServiceProvider;
use Validator;

final class ValidatorServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Validator::extend('uuid', Uuid::class);
    }

    public function register(): void
    {
        //
    }
}
