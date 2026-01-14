<?php

namespace App\Core;

use App\Services\Exceptions\AuthException;

final class Csrf
{
    // TODO: Use : ?string, and redirect upon NULL or invalid token

    /** Generate or retrieve existing CSRF token. */
    public static function getToken(): string
    {
        $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
        return $_SESSION['csrf'];
    }

    /** Verify provided CSRF token against session token.
     * @throws AuthException if token is missing or does not match. */
    public static function requireVerification(?string $token): void
    {
        if (!$token || !hash_equals($_SESSION['csrf'], $token)) {
            throw new AuthException(AuthException::CSRF_TOKEN_MISMATCH);
        }
    }
}
