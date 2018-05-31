<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CompanyCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return !$this->user();
    }

    public function rules(): array
    {
        return [
            'data.name'                  => 'required|string',
            'data.users'                 => 'required|array|size:1',
            'data.users.*.data.email'    => 'required|email|unique:users,email',
            'data.users.*.data.password' => 'required|string|min:8',
        ];
    }

    public function messages(): array
    {
        return [
            'data.name.required'                  => Messages::GENERIC_REQUIRED,
            'data.name.string'                    => Messages::GENERIC_STRING,
            'data.users.required'                 => Messages::GENERIC_REQUIRED,
            'data.users.array'                    => Messages::GENERIC_ARRAY,
            'data.users.size'                     => Messages::GENERIC_ARRAY_LENGTH_(1),
            'data.users.*.data.email.required'    => Messages::GENERIC_REQUIRED,
            'data.users.*.data.email.email'       => Messages::EMAIL_EMAIL,
            'data.users.*.data.email.unique'      => Messages::EMAIL_UNIQUE,
            'data.users.*.data.password.required' => Messages::GENERIC_REQUIRED,
            'data.users.*.data.password.string'   => Messages::GENERIC_STRING,
            'data.users.*.data.password.min'      => Messages::PASSWORD_LENGTH,
        ];
    }
}
