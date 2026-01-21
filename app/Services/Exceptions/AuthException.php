<?php

namespace App\Services\Exceptions;

/** Exceptions for authentication or authorization errors. */
final class AuthException extends \RuntimeException
{
    // Not logged in
    public const string INVALID_CREDENTIALS = 'Invalid username or password,<br>please try again.';
    public const string REQUIRES_LOGIN = 'You must log in using an account,<br>please try again.';

    // Login not known
    public const string CSRF_TOKEN_MISMATCH = 'Session expired,<br>please try again.';

    // logged in
    public const string ALREADY_LOGGED_IN = 'You are already logged in.';
    public const string PROJECT_ACCESS_DENIED = 'You do not have access to this project.';
    public const string PROJECT_INSUFFICIENT_PERMISSIONS = 'You do not have sufficient permissions for this action.';
}
