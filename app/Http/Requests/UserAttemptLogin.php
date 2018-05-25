<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UserAttemptLogin extends FormRequest
{
    public function authorize(): bool
    {
        return !$this->user();
    }

    public function rules(): array
    {
        /*
         * Minimal validation to allow the controller to do most of the work. This allows it to mask whether the
         * email or the password was invalid which improves security.
         */
        return [
            'data.email'    => 'required|email',
            'data.password' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'data.password.required' => Messages::GENERIC_REQUIRED,
            'data.email.required'    => Messages::GENERIC_REQUIRED,
            'data.email.email'       => Messages::EMAIL_EMAIL,
        ];
    }
}
