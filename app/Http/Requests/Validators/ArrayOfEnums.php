<?php
namespace App\Http\Requests\Validators;

use Illuminate\Validation\Validator;
use MyCLabs\Enum\Enum as EnumBaseClass;

final class ArrayOfEnums
{
    /**
     * @param string    $attribute
     * @param mixed     $value
     * @param array     $parameters
     * @param Validator $validator
     * @return bool
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     * @throws \UnexpectedValueException
     */
    public function validate($attribute, $value, $parameters, Validator $validator): bool
    {
        /** @var EnumBaseClass $class */
        $class = $parameters[0];
        if (!get_parent_class($class) === EnumBaseClass::class) {
            throw new \InvalidArgumentException("'$class' is not an enum");
        }

        return count(array_diff($value, $class::values())) === 0;
    }
}