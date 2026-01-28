<?php

namespace App\Core;

/** View renderer for passing view, title & parameters */
final readonly class View
{
    /** Handle rendering of views with provided title and parameters. */
    public static function render(string $view, string $title, array $extraData = []): void
    {
        // Set globally used data for views
        $data = [
            'viewFile' => __DIR__ . '/../Views/' . $view,
            'viewTitle' => $title,
            'flash_errors' => $_SESSION['flash_errors'] ?? [],
        ];

        // Merge controller data, and extract to variables for use in views
        $data = array_merge($data, $extraData);
        extract($data, EXTR_SKIP);

        // Unset all flash data, preventing showing in unrelated views
        unset($_SESSION['flash_errors']);

        self::addConditionalData($data);
        require __DIR__ . '/../Views/skeleton/base.php';
    }

    /** Retrieve site name from .env, preventing repeatedly hardcoding.
     * Optionally added to be used in View::render requests. */
    public static function addSiteName(): string
    {
        return " | " . $_ENV['SITE_NAME'];
    }

    /** Handles conditional additions to views, such as toast notifications or extra navbar elements on project views */
    private static function addConditionalData($data): void
    {
        // Include toast component if there are flash errors to show
        if ($data['flash_errors'])
            include __DIR__ . '/../Views/components/toastComp.php';
    }
}