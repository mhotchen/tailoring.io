<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CustomerStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        // See policies for more detailed authorization.
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'data.id'                => 'required|uuid',
            'data.name'              => 'required|string',
            'data.email'             => 'nullable|email',
            'data.telephone'         => 'nullable|string',
            'data.notes'             => 'required|array',
            'data.notes.*.data.id'   => 'nullable|uuid', // Allow nullable to make front end code tidier.
            'data.notes.*.data.note' => 'nullable|string|max:200',
        ];
    }

    public function messages(): array
    {
        return [
            'data.name.required'                  => Messages::GENERIC_REQUIRED,
            'data.name.string'                    => Messages::GENERIC_STRING,
            'data.notes.array'                    => Messages::GENERIC_ARRAY,
            'data.notes.*.data.note.string'       => Messages::GENERIC_STRING,
            'data.users.*.data.note.max'          => Messages::GENERIC_STRING_LENGTH_(200),
            'data.users.*.data.email.unique'      => Messages::EMAIL_UNIQUE,
            'data.users.*.data.password.required' => Messages::GENERIC_REQUIRED,
            'data.users.*.data.password.string'   => Messages::GENERIC_STRING,
            'data.users.*.data.password.min'      => Messages::PASSWORD_LENGTH,
        ];
    }
}
