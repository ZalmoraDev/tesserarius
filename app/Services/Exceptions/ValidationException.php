<?php

namespace App\Services\Exceptions;

final class ValidationException extends \RuntimeException
{
    public const string FIELDS_REQUIRED = 'fields_required';
    
    public const string USERNAME_INVALID = 'username_invalid';
    public const string USERNAME_TAKEN = 'username_taken';


    public const string EMAIL_INVALID = 'email_invalid';
    public const string EMAIL_TAKEN = 'email_taken';


    public const string PASSWORD_INVALID = 'password_invalid';
    public const string PASSWORD_MISMATCH = 'password_mismatch';

    public const string REGISTRATION_FAILED = 'registration_failed';
}