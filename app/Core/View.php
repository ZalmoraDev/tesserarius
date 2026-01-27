<?php

namespace App\Core;

/** View renderer for passing view, title & parameters */
final readonly class View
{
    /** Handle rendering of views with provided title and parameters. */
    public static function render(string $view, string $title, array $params = []): void
    {
        // All views are located in app/Views/, so this makes declaration less verbose for controllers
        $viewRender = __DIR__ . '/../Views/' . $view;
        $titleRender = $title;

        extract($params, EXTR_SKIP);

        require __DIR__ . '/../Views/skeleton/base.php';
    }

    /** Retrieve site name from .env, preventing hardcoding the name for every page title.
     * Optionally added to be used in View::render requests. */
    public static function addSiteName(): string
    {
        return " | " . $_ENV['SITE_NAME'];
    }
}