<?php

namespace App\Exceptions;

final class ValidationException extends \RuntimeException
{
    public const string USERNAME_TOO_SHORT = 'username_too_short';
    public const string USERNAME_TAKEN = 'username_taken';


    public const string EMAIL_INVALID_FORMAT = 'email_invalid_format';
    public const string EMAIL_TAKEN = 'email_taken';


    public const string WEAK_PASSWORD = 'weak_password';
    public const string PASSWORD_MISMATCH = 'password_mismatch';

}