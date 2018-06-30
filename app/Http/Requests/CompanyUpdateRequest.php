<?php
namespace App\Http\Requests;

use App\Measurement\Settings\UnitOfMeasurementSetting;
use Illuminate\Foundation\Http\FormRequest;

final class CompanyUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        // See policies for more detailed authorization.
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'data.name'                  => 'required|string|min:1',
            'data.unit_of_measurement'   => 'required|enum:'.UnitOfMeasurementSetting::class,
        ];
    }

    public function messages(): array
    {
        return [
            'data.name.required'                  => Messages::GENERIC_REQUIRED,
            'data.name.string'                    => Messages::GENERIC_STRING,
            'data.unit_of_measurement.required'   => Messages::GENERIC_REQUIRED,
            'data.unit_of_measurement.enum'       => Messages::GENERIC_ENUM,
        ];
    }
}
