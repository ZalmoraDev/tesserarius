<?php

namespace App\Controllers;

use App\Core\View;
use App\Services\ProjectServiceInterface;

final class DashboardController
{
    private ProjectServiceInterface $projectService;

    public function __construct(ProjectServiceInterface $projectService)
    {
        $this->projectService = $projectService;
    }

    /** GET, Home page for logged-in users */
    public function homePage()
    {
        // REFACTOR: Change the usage of globals, maybe give them as params?
        global $projectsAdmins, $projectsMembers;

        // Get the user ID from the sessions
        $userId = $_SESSION['auth']['userId'];

        // Fetch the projects where the user is an admin & member
        // TODO: Refactor, can easily be 1 method
        $projectsAdmins = $this->projectService->getProjectsByUserAndRole($userId, "Admin");
        $projectsMembers = $this->projectService->getProjectsByUserAndRole($userId, "Member");

        View::render('home.php', "Home" . View::addSiteName());
    }
}