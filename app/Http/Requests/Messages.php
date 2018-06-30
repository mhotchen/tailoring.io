<?php
namespace App\Http\Requests;

final class Messages
{
    public const EMAIL_EMAIL      = 'fields.email.email';
    public const EMAIL_UNIQUE     = 'fields.email.unique';
    public const GENERIC_ARRAY    = 'fields.generic.array';
    public const GENERIC_REQUIRED = 'fields.generic.required';
    public const GENERIC_STRING   = 'fields.generic.string';
    public const GENERIC_UUID     = 'fields.generic.uuid';
    public const GENERIC_ENUM     = 'fields.generic.enum';
    public const PASSWORD_LENGTH  = 'fields.password.length';
    public const PASSWORD_INVALID = 'fields.password.invalid';
    public const VERIFY_CODE_INVALID = 'fields.verify.code_invalid';

    public static function GENERIC_ARRAY_LENGTH_(int $length): string
    {
        return sprintf('fields.generic.array_length_%d', $length);
    }

    public static function GENERIC_STRING_LENGTH_(int $length): string
    {
        return sprintf('fields.generic.string_length_%d', $length);
    }
}