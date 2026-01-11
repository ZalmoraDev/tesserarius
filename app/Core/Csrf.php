<?php

namespace App\Core;

final class Csrf
{
    // TODO: Use : ?string, and redirect upon NULL or invalid token

    /** Generate or retrieve existing CSRF token. */
    public static function token(): string
    {
        $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
        return $_SESSION['csrf'];
    }

    /** Verify provided CSRF token against session token. */
    public static function verify(?string $token): void
    {
        if (!$token || !hash_equals($_SESSION['csrf'], $token)) {
            http_response_code(403);
            exit('CSRF validation failed');
        }
    }
}
