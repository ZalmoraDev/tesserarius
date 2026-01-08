<?php

namespace App\Controllers;

use App\Services\ {
    ProjectService,
    TaskService
};

class ProjectController
{
    private ProjectService $projectService;
    private TaskService $taskService;

    public function __construct($projectService, $taskService)
    {
        $this->projectService = $projectService;
        $this->taskService = $taskService;
    }

    /** GET, View a specified project by its ID */
    public function view($projectId): void
    {
        global $title, $view;
        global $allColumnTasksArray; // 2D array of tasks, holding columns and their tasks, gets split in view/Project.php

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