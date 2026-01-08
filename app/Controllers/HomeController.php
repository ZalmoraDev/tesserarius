<?php

namespace App\Controllers;

use App\Services\ProjectService;

class HomeController
{
    // TODO: Maybe change name to DashboardController? And make index method 'homePage'?
    //       Add other methods for editing user settings, etc.
    private ProjectService $projectService;

    public function __construct($projectService)
    {
        $this->projectService = $projectService;
    }

    // TODO: Change to dashboardPage()?
    /** GET, Home page for logged-in users */
    public function index()
    {
        global $title, $view;
        global $projectsAdmins, $projectsMembers;

        // Get the user ID from the session
        $userId = $_SESSION['auth']['userId'];

        // Fetch the projects where the user is an admin & member
        $projectsAdmins = $this->projectService->getProjectsByUserAndRole($userId, "admin");
        $projectsMembers = $this->projectService->getProjectsByUserAndRole($userId, "member");

        $title = "Home | Tesserarius";
        $view = __DIR__ . '/../Views/home.php';
        require __DIR__ . '/../Views/skeleton/base.php';
    }
}