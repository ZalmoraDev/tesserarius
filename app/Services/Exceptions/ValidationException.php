<?php

namespace App\Services\Exceptions;

final class ValidationException extends \RuntimeException
{
    public const string FIELDS_REQUIRED = 'All fields are required,<br>please try again.';

    public const string USERNAME_INVALID = 'Your provided username is not valid,<br>please try again.';
    public const string USERNAME_TAKEN = 'Your provided username is already taken,<br>please try again.';

    public const string EMAIL_INVALID = 'Your provided email is not valid,<br>please try again.';
    public const string EMAIL_TAKEN = 'Your provided email is already taken,<br>please try again.';

    public const string PASSWORD_INVALID = 'Your password must be in the following format:<br>at least one lowercase, one uppercase, one digit, no spaces<br>length of 12-64,<br>please try again.';
    public const string PASSWORD_MISMATCH = 'Your passwords did not match,<br>please try again.';

    public const string REGISTRATION_FAILED = 'Registration failed due to a server error,<br>please try again later.';
}