<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UserVerifyEmail extends FormRequest
{
    public function authorize(): bool
    {
        return !$this->user();
    }

    public function rules(): array
    {
        /*
         * Minimal validation to allow the controller to do most of the work since this isn't very trivial to handle
         * with validators.
         */
        return [
            'data.verification_code' => 'bail|required|uuid|exists:users,email_verification',
            'data.password'          => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'data.password.required'          => Messages::GENERIC_REQUIRED,
            'data.verification_code.required' => Messages::GENERIC_REQUIRED,
            'data.verification_code.uuid'     => Messages::GENERIC_UUID,
            'data.verification_code.exists'   => Messages::VERIFY_CODE_INVALID,
        ];
    }
}
