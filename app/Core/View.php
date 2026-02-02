<?php

namespace App\Core;

use App\Models\Enums\UserRole;
use App\Views\Components\ToastComp;

/** View renderer for passing view, title & parameters */
final readonly class View
{
    /** Handle rendering of views with provided title and parameters. */
    public static function render(string $view, string $title, array $controllerData = []): void
    {
        // Set globally used data for views
        $data = [
            'viewFile' => __DIR__ . '/../Views/Pages/' . $view,
            'viewTitle' => $title,

            // projectRole imported as string, since otherwise each route would need to import UserRole enum
            'user' => [
                'id' => $_SESSION['auth']['userId'] ?? null,
                'username' => $_SESSION['auth']['username'] ?? null,
                'email' => $_SESSION['auth']['userEmail'] ?? null,
                'role' => UserRole::tryFrom($_SESSION['auth']['projectRole'] ?? '') ?? null,
            ],

            'flash' => [
                'successes' => $_SESSION['flash_successes'] ?? [],
                'info' => $_SESSION['flash_info'] ?? [],
                'errors' => $_SESSION['flash_errors'] ?? [],
            ],

            'csp_nonce' => $_SESSION['csp_nonce'] ?? '',
        ];

        // Merge controller data, and extract to variables for use in Views
        $data = array_merge($data, $controllerData);
        self::addToastNotifications($data);
        extract($data, EXTR_SKIP);

        // Unset all flash data, preventing showing in unrelated views
        unset(
            $_SESSION['flash_successes'],
            $_SESSION['flash_info'],
            $_SESSION['flash_errors']
        );
        require __DIR__ . '/../Views/Layouts/base.php';
    }

    /** Retrieve site name from .env, preventing repeatedly hardcoding.
     * Optionally added to be used in View::render requests. */
    public static function addSiteName(): string
    {
        return " | " . $_ENV['SITE_NAME'];
    }

    /** Handles conditional additions to views, such as toast notifications or extra navbar elements on project views */
    private static function addToastNotifications($data): void
    {
        // Render toast component if there are flash messages to show
        if ($data['flash']['successes'] || $data['flash']['info'] || !empty($data['flash']['errors']))
            ToastComp::render($data['flash'], $data['csp_nonce']);
    }
}