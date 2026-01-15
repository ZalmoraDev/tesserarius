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

    // -------------------- GET Requests --------------------

    /** GET /, Home page for logged-in users */
    public function homePage()
    {
        // Owner        = owned  = "Your Projects"
        // Member/Admin = member = "Member Projects"
        $projects = $this->projectService->getDashboardProjects((int)$_SESSION['auth']['userId']);
        View::render('/Dashboard/home.php', "Home" . View::addSiteName(), ['projects' => $projects]);
    }

    /* GET /dashboard/create, serves project creation page */
    public function createProjectPage()
    {
        View::render('create_project.php', "Create Project" . View::addSiteName());
    }

    // -------------------- POST Requests --------------------

    /** POST /dashboard/create, handles project creation form submission */
    public function createProject() {

    }
}