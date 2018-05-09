<?php
namespace App\Http\Requests;

final class Messages
{
    public const EMAIL_EMAIL      = 'fields.email.email';
    public const EMAIL_UNIQUE     = 'fields.email.unique';
    public const GENERIC_ARRAY    = 'fields.generic.array';
    public const GENERIC_REQUIRED = 'fields.generic.required';
    public const GENERIC_STRING   = 'fields.generic.string';
    public const PASSWORD_LENGTH  = 'fields.password.length';

    public static function GENERIC_ARRAY_LENGTH_(int $length): string
    {
        return sprintf('fields.generic.array_length_%d', $length);
    }
}