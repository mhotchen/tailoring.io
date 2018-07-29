<?php
namespace App\Http\Requests;

use App\Garment\GarmentType;
use Illuminate\Foundation\Http\FormRequest;

final class SampleGarmentCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        // See policies for more detailed authorization.
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'data.name'     => 'required|string|min:1|max:50',
            'data.garment'  => 'required|enum:' . GarmentType::class,
        ];
    }

    public function messages(): array
    {
        return [
            'data.name.required'           => Messages::GENERIC_REQUIRED,
            'data.name.string'             => Messages::GENERIC_STRING,
            'data.name.min'                => Messages::GENERIC_STRING_MIN_LENGTH_(1),
            'data.name.max'                => Messages::GENERIC_STRING_MAX_LENGTH_(50),
            'data.garment.required'        => Messages::GENERIC_REQUIRED,
            'data.garment.enum'            => Messages::GENERIC_ENUM,
        ];
    }
}