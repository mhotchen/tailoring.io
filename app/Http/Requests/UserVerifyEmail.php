<?php

namespace App\Http\Requests;

use App\Model\User;
use Hash;
use Illuminate\Foundation\Http\FormRequest;
use Ramsey\Uuid\Uuid;

final class UserVerifyEmail extends FormRequest
{
    public function authorize(): bool
    {
        return !$this->user();
    }

    public function rules(): array
    {
        return [
            'data.verification_code' => 'bail|required|uuid|exists:users,email_verification',
            'data.password' => [
                'required',
                function ($attribute, $value, $fail) {
                    /*
                     * It's extremely unlikely that someone can guess the verification code but sleep for a second
                     * anyway to make brute force attacks impractical.
                     */
                    sleep(1);

                    $code = data_get($this->all(), 'data.verification_code');
                    if (!$code || !Uuid::isValid($code)) {
                        // Let the UUID validator handle error messages when the verification code is invalid.
                        return null;
                    }

                    $user = User::whereEmailVerification($code)
                        ->where(['status' => User::STATUS_AWAITING_EMAIL_VERIFICATION])
                        ->first();

                    if (!$user) {
                        // Let the UUID validator handle error messages when the verification code is invalid.
                        return null;
                    }

                    if (!Hash::check($value, $user->password)) {
                        return $fail(Messages::PASSWORD_INVALID);
                    }

                    return null;
                },
            ],
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
