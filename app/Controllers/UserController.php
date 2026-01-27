<?php

namespace App\Controllers;

use App\Core\View;
use App\Services\ProjectServiceInterface;

/** Controller for user-related actions
 * - GET: Display user homepage and settings */
final readonly class UserController
{
    private ProjectServiceInterface $projectService;
    public function __construct(ProjectServiceInterface $projectService)
    {
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
}