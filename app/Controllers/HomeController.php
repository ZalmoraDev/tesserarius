<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\ProjectService;

class HomeController
{
    private AuthService $authService;
    private ProjectService $projectService;

    public function __construct($authService, $projectService)
    {
        $this->authService = $authService;
        $this->projectService = $projectService;
    }

    public function index()
    {
        $this->authService->checkIfLoggedIn(); // If not logged in, redirect to login page

        global $title, $view;
        global $projectsAdmins, $projectsMembers;

        // Get the user ID from the session
        $userId = $_SESSION['userId'];

        // Fetch the projects where the user is an admin & member
        $projectsAdmins = $this->projectService->getProjectsByUserAndRole($userId, "admin");
        $projectsMembers = $this->projectService->getProjectsByUserAndRole($userId, "member");

        $title = "Home | Tesserarius";
        $view = __DIR__ . '/../Views/home.php';
        require __DIR__ . '/../Views/skeleton/base.php';
    }
}