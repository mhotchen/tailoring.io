<?php

namespace App\Http\Requests\Validators;

use Illuminate\Validation\Validator;
use Ramsey\Uuid\Uuid as RamseyUuid;

final class Uuid
{
    public function validate($attribute, $value, $parameters, Validator $validator): bool
    {
        return RamseyUuid::isValid($value);
    }
}