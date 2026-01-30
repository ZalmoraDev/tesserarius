<?php

namespace App\Controllers;

use App\Core\View;
use App\Services\Exceptions\AuthException;
use App\Services\Exceptions\ValidationException;
use App\Services\Interfaces\ProjectServiceInterface;
use App\Services\Interfaces\UserServiceInterface;

/** Controller for user-related actions
 * - GET: Display user homepage and settings
 * - POST: Handle user account edits and deletions */
final readonly class UserController
{
    private UserServiceInterface $userService;
    private ProjectServiceInterface $projectService;

    public function __construct(UserServiceInterface $userService, ProjectServiceInterface $projectService)
    {
        $this->userService = $userService;
        $this->projectService = $projectService;
    }

    // -------------------- GET Requests --------------------

    /** GET /, Home page for logged-in users */
    public function homePage()
    {
        // Owner        = owned  = "Your Projects"
        // Member/Admin = member = "Member Projects"
        $projects = $this->projectService->getHomeProjects((int)$_SESSION['auth']['userId']);
        View::render('/User/home.php', "Home" . View::addSiteName(), [
            'projects' => $projects
        ]);
    }

    public function settingsPage()
    {
        View::render('/User/settings.php', "Settings" . View::addSiteName());
    }

    // -------------------- POST Requests --------------------

    public function handleEdit(): void
    {
        try {
            $this->userService->editAccount(
                $_POST['username'] ?? '',
                $_POST['email'] ?? ''
            );
            $_SESSION['flash_successes'][] = "Account updated successfully.";
        } catch (ValidationException $e) {
            $_SESSION['flash_errors'][] = $e->getMessage();
        }
        header("Location: /settings", true, 302);
        exit;
    }

    public function handleDeletion(): void
    {
        try {
            $this->userService->deleteAccount(
                $_POST['confirm_username'] ?? ''
            );
            $_SESSION['flash_successes'][] = "Account deleted successfully.";
            header("Location: /login", true, 302);
            exit;
        } catch (AuthException $e) {
            $_SESSION['flash_errors'][] = $e->getMessage();
            header("Location: /settings", true, 302);
            exit;
        }
    }
}