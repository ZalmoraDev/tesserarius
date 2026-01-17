<?php

namespace App\Controllers;

use App\Core\View;
use App\Services\{Exceptions\ProjectException, ProjectServiceInterface, TaskServiceInterface};

final class ProjectController
{
    private ProjectServiceInterface $projectService;
    private TaskServiceInterface $taskService;

    public function __construct(ProjectServiceInterface $projectService, TaskServiceInterface $taskService)
    {
        $this->projectService = $projectService;
        $this->taskService = $taskService;
    }

    // -------------------- GET Requests --------------------

    /** GET /projects/create, serves project creation page */
    public function showCreate()
    {
        View::render('/Project/projectCreate.php', "Create Project" . View::addSiteName());
    }

    /** GET /projects/{projectId}, View a specified project by its ID */
    public function showView($projectId): void
    {
        $project = $this->projectService->getProjectByProjectId($projectId);

        // Get all tasks for the project (2D array of tasks, holding columns and their tasks)
        $allColumnTasksArray = $this->taskService->getAllColumnTasks($projectId);

        View::render('/Project/projectView.php', $project->name . View::addSiteName(), ['project' => $project, 'allColumnTasksArray' => $allColumnTasksArray]);
    }

    // -------------------- POST Requests --------------------

    /** POST /projects, handles project creation form submission */
    public function handleCreate() {
        try {
            $id = $this->projectService->createProject(
                $_POST['name'] ?? '',
                $_POST['description'] ?? ''
            );
            header("Location: /project/view/" . $id, true, 302);
            exit;
        } catch (ProjectException $e) {
            $_SESSION['flash_errors'][] = $e->getMessage();
            header("Location: /project/create", true, 302);
            exit;
        }
    }
}