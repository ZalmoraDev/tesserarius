<?php

namespace App\Services\Exceptions;

/** Exceptions for authentication or authorization errors. */
final class AuthException extends \RuntimeException
{
    // Not logged in
    public const string INVALID_CREDENTIALS = 'invalid_credentials';
    public const string REQUIRES_LOGIN = 'requires_login';

    // Login not known
    public const string CSRF_TOKEN_MISMATCH = 'csrf_token_mismatch';

    // logged in
    public const string ALREADY_LOGGED_IN = 'already_logged_in';
    public const string PROJECT_ACCESS_DENIED = 'project_access_denied';
    public const string PROJECT_INSUFFICIENT_PERMISSIONS = 'project_insufficient_permissions';

    public function __construct(string $reason)
    {
        parent::__construct($reason);
    }

    /** Get the reason for the exception,
     * used for redirection to /login or / (home) depending on if the user was authenticated, used in the router.
     */
    public function reason(): string
    {
        return $this->getMessage();
    }
}
