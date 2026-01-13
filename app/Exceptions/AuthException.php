<?php

namespace App\Exceptions;

final class AuthException extends \RuntimeException
{
    public const string INVALID_CREDENTIALS = 'invalid_credentials';
    public const string REGISTRATION_FAILED = 'registration_failed';
    public const string INSUFFICIENT_PERMISSIONS = 'insufficient_permissions';
}
