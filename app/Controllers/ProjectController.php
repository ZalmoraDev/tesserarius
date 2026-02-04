<?php

namespace App\Controllers;

use App\Core\View;
use Exception;
use App\Services\{Exceptions\ProjectException,
    Interfaces\ProjectServiceInterface,
    Interfaces\TaskServiceInterface,
    ProjectMembersService
};

/** Controller handling project requests
 * - GET: Displaying project creation, viewing, and editing pages
 * - POST: Processing project creation, editing, and deletion form submissions */
final readonly class ProjectController
{
    private ProjectServiceInterface $projectService;
    private ProjectMembersService $projectMemberService;
    private TaskServiceInterface $taskService;

    public function __construct(ProjectServiceInterface $projectService, ProjectMembersService $projectMembersService, TaskServiceInterface $taskService)
    {
        $this->projectService = $projectService;
        $this->projectMemberService = $projectMembersService;
        $this->taskService = $taskService;
    }

    //region GET Requests
    /** GET /projects/create, serves project creation page */
    public function showCreate()
    {
        View::render('/Project/projectCreate.php', "Create Project" . View::addSiteName());
    }

    /** GET /project/view/{$projectId}, View a specified project by its ID */
    public function showView($projectId): void
    {
        $project = $this->projectService->getProjectByProjectId($projectId);
        $members = $this->projectMemberService->getProjectMembersByProjectId($projectId);
        $tasks = $this->taskService->getAllProjectTasks($projectId);

        View::render('/Project/projectView.php', $project->name . View::addSiteName(), [
            'project' => $project,
            'members' => $members,
            'tasks' => $tasks
        ]);
    }

    /** GET /project/edit/{$projectId}, View a specified project by its ID */
    public function showEdit($projectId): void
    {
        $project = $this->projectService->getProjectByProjectId($projectId);
        $members = $this->projectMemberService->getProjectMembersByProjectId($projectId);
        $invites = $this->projectMemberService->getProjectInvitesByProjectId($projectId);

        View::render('/Project/projectEdit.php', $project->name . View::addSiteName(), [
            'project' => $project,
            'members' => $members,
            'invites' => $invites,
        ]);
    }
    //endregion


    //region POST Requests
    /** POST /project/create, handles project creation form submission */
    public function handleCreate()
    {
        try {
            $id = $this->projectService->createProject(
                $_POST['name'] ?? '',
                $_POST['description'] ?? ''
            );
            $_SESSION['flash_successes'][] = "Project created successfully.";
            $redirect = "/project/view/" . $id;
        } catch (ProjectException $e) {
            $_SESSION['flash_errors'][] = $e->getMessage();
            $redirect = "/project/create";
        } catch (Exception) {
            $_SESSION['flash_errors'][] = "An unexpected error occurred.";
            $redirect = "/project/create";
        }
        header("Location: $redirect", true, 302);
        exit;
    }

    /** POST /project/edit/{$projectId}, handles the creation of a project invite */
    public function handleEdit(int $projectId)
    {
        try {
            $this->projectService->editProject(
                $projectId,
                $_POST['name'] ?? '',
                $_POST['description'] ?? '',
                $_POST['projectName'] ?? '');
            $_SESSION['flash_successes'][] = "Project updated successfully.";
        } catch (ProjectException $e) {
            $_SESSION['flash_errors'][] = $e->getMessage();
        } catch (\Exception) {
            $_SESSION['flash_errors'][] = "An unexpected error occurred.";
        }
        header("Location: /project/edit/" . $projectId, true, 302);
        exit;
    }

    /** POST /project/delete/{$projectId}, handles the creation of a project invite */
    public function handleDeletion(int $projectId)
    {
        try {
            $this->projectService->deleteProject(
                $projectId,
                $_POST['confirm_name'] ?? ''
            );
            $_SESSION['flash_successes'][] = "Project deleted successfully.";
            $redirect = "/";
        } catch (ProjectException $e) {
            $_SESSION['flash_errors'][] = $e->getMessage();
            $redirect = "/project/edit/" . $projectId;
        } catch (\Exception) {
            $_SESSION['flash_errors'][] = "An unexpected error occurred.";
            $redirect = "/project/edit/" . $projectId;
        }
        header("Location: $redirect" . $projectId, true, 302);
        exit;
    }
    //endregion
}