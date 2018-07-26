<?php
namespace App\Http\Requests\Validators;

use App\Measurement\MeasurementType;
use Illuminate\Support\Arr;
use Illuminate\Validation\Validator;

final class GarmentsCount
{
    /**
     * Quite a specific validator but off the top of my head I can't think of a better solution.
     *
     * The first and only parameter to the validator must be the field that contains the measurement type.
     *
     * @param string    $attribute
     * @param mixed     $value
     * @param array     $parameters
     * @param Validator $validator
     * @return bool
     * @throws \UnexpectedValueException
     */
    public function validate($attribute, $value, $parameters, Validator $validator): bool
    {
        // Body measurements can be associated with more than one garment type, all others must be associated with
        // exactly one garment type.
        switch (new MeasurementType(Arr::get($validator->getData(), $parameters[0]))) {
            case MeasurementType::BODY():
                return count($value) > 0;
            default:
                return count($value) === 1;
        }
    }
}