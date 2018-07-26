<?php

namespace App\Providers;

use App\Http\Requests\Validators\ArrayOfEnums;
use App\Http\Requests\Validators\Enum;
use App\Http\Requests\Validators\GarmentsCount;
use App\Http\Requests\Validators\Uuid;
use Illuminate\Support\ServiceProvider;
use Validator;

final class ValidatorServiceProvider extends ServiceProvider
{
    private $validators = [
        'uuid'           => Uuid::class,
        'enum'           => Enum::class,
        'array_of_enums' => ArrayOfEnums::class,
        'garments_count' => GarmentsCount::class,
    ];

    public function boot(): void
    {
        foreach ($this->validators as $key => $validator) {
            Validator::extend($key, $validator);
        }
    }

    public function register(): void
    {
        //
    }
}
