<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\ProjectService;

class HomeController
{
    private ProjectService $projectService;

    public function __construct($projectService)
    {
        $this->projectService = $projectService;
    }

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