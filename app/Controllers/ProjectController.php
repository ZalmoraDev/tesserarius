<?php

namespace App\Controllers;

use App\Core\View;
use App\Services\{ProjectServiceInterface, TaskServiceInterface};

final class ProjectController
{
    private ProjectServiceInterface $projectService;
    private TaskServiceInterface $taskService;

    public function __construct(ProjectServiceInterface $projectService, TaskServiceInterface $taskService)
    {
        $this->projectService = $projectService;
        $this->taskService = $taskService;
    }

    /** GET, View a specified project by its ID */
    public function view($projectId): void
    {
        // REFACTOR: Move global variable usage out of controller
        global $allColumnTasksArray; // 2D array of tasks, holding columns and their tasks, gets split in view/Project.php

        // Get project name to set the title
        // TODO: Unnecessary double project retrieval in controller and view
        $project = $this->projectService->getProjectByProjectId($projectId);

        // Get all tasks for the project (2D array of tasks, holding columns and their tasks)
        $allColumnTasksArray = $this->taskService->getAllColumnTasks($projectId);

        View::render('project.php', $project->getName() . View::getSiteName());
    }
}