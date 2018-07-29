<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class SampleGarmentUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        // See policies for more detailed authorization.
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'data.name' => 'required|string|min:1|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'data.name.required' => Messages::GENERIC_REQUIRED,
            'data.name.string'   => Messages::GENERIC_STRING,
            'data.name.min'      => Messages::GENERIC_STRING_MIN_LENGTH_(1),
            'data.name.max'      => Messages::GENERIC_STRING_MAX_LENGTH_(50),
        ];
    }
}
