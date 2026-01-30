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


    // User account actions
    public const string DELETION_REQUIRES_CONFIRMATION = 'You must confirm your username to delete your account.';
    public const string DELETION_NAME_MISMATCH = 'The provided username does not match your username.';
    public const string DELETION_FAILED = 'Account deletion failed due to a server error,<br>please try again later.';
}
