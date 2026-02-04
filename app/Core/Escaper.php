<?php
namespace App\Core;

/** Utility class for escaping strings for safe output in different contexts. */
final readonly class Escaper
{
    /** Escape HTML special characters in a string to prevent XSS attacks */
    public static function html(?string $string): string
    {
        return htmlspecialchars($string ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}