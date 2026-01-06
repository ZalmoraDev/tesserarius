<?php

namespace App\Controllers;

use App\Services\ {
    AuthService,
    ProjectService,
    TaskService
};

class ProjectController
{
    private AuthService $authService;
    private ProjectService $projectService;
    private TaskService $taskService;

    public function __construct($authService, $projectService, $taskService)
    {
        $this->authService = $authService;
        $this->projectService = $projectService;
        $this->taskService = $taskService;
    }

    public function view($projectId): void
    {
        global $title, $view;
        global $allColumnTasksArray; // 2D array of tasks, holding columns and their tasks, gets split in view/Project.php

        // Check if authorized. If not member/admin -> redirect to login page
        $this->authService->shouldProjectBeAccessible($projectId);

        // Get project name to set the title
        // TODO: Unnecessary double project retrieval in controller and view
        $project = $this->projectService->getProjectByProjectId($projectId);

        // Get all tasks for the project (2D array of tasks, holding columns and their tasks)
        $allColumnTasksArray = $this->taskService->getAllColumnTasks($projectId);

        $title = $project->getName() . " | Tesserarius";
        $view = __DIR__ . '/../Views/project.php';
        require __DIR__ . '/../Views/skeleton/base.php';
    }
}