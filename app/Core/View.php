<?php

namespace App\Core;

final class View
{
    /** Retrieve from .env the site name, preventing hardcoding the name for every pagetitle */
    public static function getSiteName(): string
    {
        return " | " . $_ENV['SITE_NAME'];
    }

    public static function render(string $view, string $title, array $params = []): void
    {
        // All views are located in app/Views/, so this makes declaration less verbose
        $view = __DIR__ . '/../Views/' . $view;
        extract($params, EXTR_SKIP);

        require __DIR__ . '/../Views/skeleton/base.php';
    }
}