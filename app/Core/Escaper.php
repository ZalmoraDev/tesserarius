<?php
namespace App\Core;

final readonly class Escaper
{
    /** Escape HTML special characters in a string to prevent XSS attacks */
    public static function html(string $string): string
    {
        return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    public static function js(string $string): string
    {
        // TODO: Implement if needed
        return '';
    }
}