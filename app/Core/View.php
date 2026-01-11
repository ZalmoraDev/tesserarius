<?php

namespace App\Core;

final class View
{
    /** Retrieve site name from .env, preventing hardcoding the name for every pagetitle.
     * Optionally added to View::render requests.
     */
    public static function addSiteName(): string
    {
        return " | " . $_ENV['SITE_NAME'];
    }

    public static function render(string $view, string $title, array $params = []): void
    {
        // All views are located in app/Views/, so this makes declaration less verbose for controllers
        $viewRender = __DIR__ . '/../Views/' . $view;
        $titleRender = $title;

        extract($params, EXTR_SKIP);

        require __DIR__ . '/../Views/skeleton/base.php';
    }
}