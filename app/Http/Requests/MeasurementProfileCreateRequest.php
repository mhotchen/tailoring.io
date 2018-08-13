<?php
namespace App\Http\Requests;

use App\Garment\GarmentType;
use App\Measurement\Profile\MeasurementProfileType;
use Illuminate\Foundation\Http\FormRequest;

final class MeasurementProfileCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        // See policies for more detailed authorization.
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'data.id'      => 'required|uuid',
            'data.type'    => 'required|enum:' . MeasurementProfileType::class,
            'data.garment' => 'nullable|enum:' . GarmentType::class,
        ];
    }

    public function messages(): array
    {
        return [
            'data.id.required'   => Messages::GENERIC_REQUIRED,
            'data.id.uuid'       => Messages::GENERIC_UUID,
            'data.type.required' => Messages::GENERIC_REQUIRED,
            'data.type.enum'     => Messages::GENERIC_ENUM,
            'data.garment.enum'  => Messages::GENERIC_STRING_MIN_LENGTH_(1),
        ];
    }
}