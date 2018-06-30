<?php
namespace App\Http\Requests\Validators;

use Illuminate\Validation\Validator;
use MyCLabs\Enum\Enum as EnumBaseClass;

final class Enum
{
    public function validate($attribute, $value, $parameters, Validator $validator): bool
    {
        $class = $parameters[0];
        return get_parent_class($class) === EnumBaseClass::class && $class::isValid($value);
    }
}