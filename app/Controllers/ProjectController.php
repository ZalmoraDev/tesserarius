<?php

namespace App\Controllers;

use App\Services\AuthService;

use App\Services\TaskService;
use App\Services\ProjectService;

class ProjectController
{
    public function view($projectId): void
    {
        global $title, $view;
        global $allColumnTasksArray; // 2D array of tasks, holding columns and their tasks, gets split in view/Project.php

        // Check if authorized. If not member/admin -> redirect to login page
        $authService = new AuthService();
        $authService->shouldProjectBeAccessible($projectId);

        // Get project name to set the title
        $projectService = new ProjectService();
        $project = $projectService->getProjectByProjectId($projectId); //TODO: Unnecessary double project retrieval in controller and view

        // Get all tasks for the project (2D array of tasks, holding columns and their tasks)
        $taskService = new TaskService();
        $allColumnTasksArray = $taskService->getAllColumnTasks($projectId);

        $title = $project->getName() . " | Tesserarius";
        $view = __DIR__ . '/../Views/project.php';

        require __DIR__ . '/../Views/skeleton/base.php'; // Base.php is the template file, it will be used to wrap the content of the view file with default markup
    }
}