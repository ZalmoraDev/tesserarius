<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\ProjectService;

class HomeController
{
    private ProjectService $projectService;

    public function __construct()
    {
        $authService = new AuthService();
        $authService->checkIfLoggedIn(); // If not logged in, redirect to login page

        $this->projectService = new ProjectService();
    }

    public function index()
    {
        global $title, $view;
        global $projectsAdmins, $projectsMembers;

        $title = "Home | Tesserarius";
        $view = __DIR__ . '/../Views/Home.php';

        // Get the user ID from the session
        $userId = $_SESSION['userId'];

        // Fetch the projects where the user is an admin & member
        $projectsAdmins = (array)$this->projectService->getProjectsByUserAndRole($userId, "admin");
        $projectsMembers = (array)$this->projectService->getProjectsByUserAndRole($userId, "member");

        require __DIR__ . '/../Views/skeleton/Base.php'; // Base.php is the template file, it will be used to wrap the content of the view file with default markup
    }
}