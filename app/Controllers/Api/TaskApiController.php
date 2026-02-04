<?php

namespace App\Controllers\Api;

use App\Models\Enums\AccessRole;
use App\Models\Enums\TaskPriority;
use App\Models\Enums\TaskStatus;
use App\Models\Task;
use App\Services\Exceptions\AuthException;
use App\Services\Exceptions\ServiceException;
use App\Services\Interfaces\AuthServiceInterface;
use App\Services\Interfaces\TaskServiceInterface;
use DateTimeImmutable;
use Exception;

/** Controller handling API requests related to tasks */
final class TaskApiController extends BaseApiController
{
    private TaskServiceInterface $taskService;

    public function __construct(AuthServiceInterface $authService, TaskServiceInterface $taskService)
    {
        parent::__construct($authService);
        $this->taskService = $taskService;
    }

    //region POST Requests

    /** POST /api/tasks/create, handles task creation */
    public function handleCreation(): void
    {
        try {
            $task = $this->extractTaskFromPost();
            $this->authenticateRequest($task->projectId, AccessRole::Member);

            $createdTask = $this->taskService->createTask($task, $_SESSION['auth']['userId']);

            $this->jsonSuccess(201, [
                'message' => 'Task created successfully',
                'task' => $createdTask->jsonSerialize()
            ]);
        } catch (AuthException $e) {
            $this->jsonError(403, $e->getMessage());
        } catch (ServiceException $e) {
            $this->jsonError(400, $e->getMessage());
        } catch (Exception) {
            $this->jsonError(500, 'An unexpected error occurred');
        }
    }

    /** POST /api/tasks/edit, handles task editing/updating */
    public function handleEdit(): void
    {
        try {
            $task = $this->extractTaskFromPost();
            $this->authenticateRequest($task->projectId, AccessRole::Member);

            $updatedTask = $this->taskService->updateTask($task);

            $this->jsonSuccess(200, [
                'message' => 'Task updated successfully',
                'task' => $updatedTask->jsonSerialize()
            ]);
        } catch (AuthException $e) {
            $this->jsonError(403, $e->getMessage());
        } catch (ServiceException $e) {
            $this->jsonError(400, $e->getMessage());
        } catch (Exception $e) {
            $this->jsonError(500, 'An unexpected error occurred');
        }
    }

    /** POST /api/tasks/delete, handles task deletion */
    public function handleDeletion(): void
    {
        try {
            $task = $this->extractTaskFromPost();
            $this->authenticateRequest($task->projectId, AccessRole::Member);

            $this->taskService->deleteTask($task->id);
            $this->jsonSuccess(200, [
                'message' => 'Task deleted successfully'
            ]);
        } catch (AuthException $e) {
            $this->jsonError(403, $e->getMessage());
        } catch (ServiceException $e) {
            $this->jsonError(400, $e->getMessage());
        } catch (Exception $e) {
            $this->jsonError(500, 'An unexpected error occurred');
        }
    }
    //endregion


    //region Helpers
    /**
     * Since all POST requests to this controller come through a modal popup form with the same fields,
     * we can extract the task data once from the same $_POST, instead of doing so multiple times.
     * @return Task extracted from POST data (some fields may be empty, to be validated/filled later)
     */
    private function extractTaskFromPost(): Task
    {
        $assigneeId = filter_var($_POST['assignee'] ?? null, FILTER_VALIDATE_INT);

        return new Task(
            id: filter_var($_POST['task_id'] ?? 0, FILTER_VALIDATE_INT) ?: 0,
            projectId: filter_var($_POST['project_id'] ?? 0, FILTER_VALIDATE_INT) ?: 0,
            title: trim($_POST['title'] ?? ''),
            description: trim($_POST['description'] ?? ''),
            status: TaskStatus::tryFrom($_POST['status'] ?? '') ?? TaskStatus::Backlog,
            priority: TaskPriority::tryFrom($_POST['priority'] ?? '') ?? TaskPriority::None,
            creatorId: 0, // Will be set by service
            assigneeId: is_int($assigneeId) ? $assigneeId : null,
            creationDate: new DateTimeImmutable(), // Placeholder
            dueDate: !empty($_POST['due_date'] ?? '') ? new DateTimeImmutable($_POST['due_date']) : null
        );
    }
    //endregion
}