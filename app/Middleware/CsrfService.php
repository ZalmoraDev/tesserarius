<?php

namespace App\Middleware;
final class CsrfService
{
    public function token(): string
    {
        $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
        return $_SESSION['csrf'];
    }

    public function verify(?string $token): void
    {
        if (!$token || !hash_equals($_SESSION['csrf'], $token)) {
            http_response_code(403);
            exit('CSRF validation failed');
        }
    }
}
