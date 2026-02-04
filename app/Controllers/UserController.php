<?php

namespace App\Controllers;

use App\Core\View;
use App\Services\Exceptions\ServiceException;
use App\Services\Interfaces\ProjectServiceInterface;
use App\Services\Interfaces\UserServiceInterface;
use Exception;

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

    //region GET Requests
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

    /** GET /settings, User account settings page */
    public function settingsPage()
    {
        View::render('/User/settings.php', "Settings" . View::addSiteName());
    }
    //endregion


    //region POST Requests
    /** POST /settings/edit, Handle user account edit form submission */
    public function handleEdit(): void
    {
        try {
            $this->userService->editAccount(
                $_POST['username'] ?? '',
                $_POST['email'] ?? ''
            );
            $_SESSION['flash_successes'][] = "Account updated successfully.";
        } catch (ServiceException $e) {
            $_SESSION['flash_errors'][] = $e->getMessage();
        } catch (Exception) {
            $_SESSION['flash_errors'][] = "An unexpected error occurred.";
        }
        $redirect = "/settings";
        header("Location: $redirect", true, 302);
        exit;
    }

    /** POST /settings/delete, Handle user account deletion form submission */
    public function handleDeletion(): void
    {
        try {
            $this->userService->deleteAccount(
                $_POST['confirm_username'] ?? ''
            );
            $_SESSION['flash_successes'][] = "Account deleted successfully.";
            $redirect = "/login";
        } catch (ServiceException $e) {
            $_SESSION['flash_errors'][] = $e->getMessage();
            $redirect = "/settings";
        } catch (Exception) {
            $_SESSION['flash_errors'][] = "An unexpected error occurred.";
            $redirect = "/settings";
        }
        header("Location: $redirect", true, 302);
        exit;
    }
}