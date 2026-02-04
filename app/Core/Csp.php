<?php
namespace App\Core;

/** Content Security Policy (CSP) utility class for generating nonces for inline scripts. */
final readonly class Csp
{
    /** Generate a new CSP-nonce for each page request, and set it in session to be used by View.php for use within inline scripts.
     * @return string The generated nonce string, used to set index.php header and script tag. */
    public static function getNonce(): string
    {
        // Always regenerate nonce on each request (not persisted across requests)
        $_SESSION['csp_nonce'] = base64_encode(random_bytes(16));
        return $_SESSION['csp_nonce'];
    }
}