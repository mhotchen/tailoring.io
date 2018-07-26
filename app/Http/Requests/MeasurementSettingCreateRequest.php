<?php
namespace App\Http\Requests;

use App\Garment\GarmentType;
use App\Measurement\MeasurementType;
use Illuminate\Foundation\Http\FormRequest;

final class MeasurementSettingCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        // See policies for more detailed authorization.
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'data.name'     => 'required|string|min:1|max:20',
            'data.type'     => 'required|enum:' . MeasurementType::class,
            'data.garments' => implode('|', [
                'required',
                'array',
                'array_of_enums:' . GarmentType::class,
                'garments_count:data.type',
            ]),
            'data.min_value' => 'required|integer',
            'data.max_value' => 'required|integer|gt:data.min_value',
        ];
    }

    public function messages(): array
    {
        return [
            'data.name.required'           => Messages::GENERIC_REQUIRED,
            'data.name.string'             => Messages::GENERIC_STRING,
            'data.name.min'                => Messages::GENERIC_STRING_MIN_LENGTH_(1),
            'data.name.max'                => Messages::GENERIC_STRING_MAX_LENGTH_(20),
            'data.type.required'           => Messages::GENERIC_REQUIRED,
            'data.type.in'                 => Messages::GENERIC_INVALID,
            'data.garments.required'       => Messages::GENERIC_REQUIRED,
            'data.garments.string'         => Messages::GENERIC_STRING,
            'data.garments.array_of_enums' => Messages::GENERIC_INVALID,
            'data.garments.garments_count' => Messages::GENERIC_INVALID,
            'data.min_value.required'      => Messages::GENERIC_REQUIRED,
            'data.min_value.integer'       => Messages::GENERIC_INTEGER,
            'data.max_value.required'      => Messages::GENERIC_REQUIRED,
            'data.max_value.integer'       => Messages::GENERIC_INTEGER,
            'data.max_value.gt'            => Messages::GENERIC_INVALID,
        ];
    }
}