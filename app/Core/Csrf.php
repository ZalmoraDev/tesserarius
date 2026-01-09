<?php

namespace App\Core;
use Random\RandomException;

final class Csrf
{
    /** Generate or retrieve existing CSRF token.
     * Used by HTML forms.
     * @throws RandomException
     */
    public function token(): string
    {
        $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
        return $_SESSION['csrf'];
    }

    /** Verify provided CSRF token against session token. */
    public function verify(?string $token): void
    {
        if (!$token || !hash_equals($_SESSION['csrf'], $token)) {
            http_response_code(403);
            exit('CSRF validation failed');
        }
    }
}
