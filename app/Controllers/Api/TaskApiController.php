<?php

namespace App\Controllers\Api;

use App\Core\Csrf;
use App\Models\Enums\AccessRole;
use App\Services\Exceptions\AuthException;
use App\Services\Exceptions\TaskException;
use App\Services\Interfaces\AuthServiceInterface;
use App\Services\Interfaces\TaskServiceInterface;
use Exception;

class TaskApiController extends BaseApiController
{
    private TaskServiceInterface $taskService;

    public function __construct(AuthServiceInterface $authService, TaskServiceInterface $taskService)
    {
        parent::__construct($authService);
        $this->taskService = $taskService;
    }

    public function handleCreation(): void
    {
        // Set JSON response header
        header('Content-Type: application/json');

        try {
            // Verify CSRF token
            Csrf::requireVerification($_POST['csrf'] ?? null);

            // Get current user ID from session
            $userId = $this->getCurrentUserId();
            if (!$userId) {
                $this->jsonError(401, 'Not authenticated');
                return;
            }

            // Extract and validate form data
            $data = $this->extractTaskDataFromPost();

            // Validate required fields
            if (!$data['projectId']) {
                $this->jsonError(400, 'Invalid project ID');
                return;
            }

            if (empty($data['title'])) {
                $this->jsonError(400, 'Title is required');
                return;
            }

            // SECURITY: Validate user still has access to the project (prevents removed users from creating tasks)
            $this->requireProjectAccess($data['projectId'], AccessRole::Member);

            // Create task through service
            $task = $this->taskService->createTask(
                $data['projectId'],
                $data['title'],
                $data['description'] ?: null,
                $data['status'],
                $data['priority'],
                $userId,
                $data['assigneeId'] ?: null,
                $data['dueDate']
            );

            // Return success response with created task data
            $this->jsonSuccess(201, [
                'message' => 'Task created successfully',
                'task' => $task->jsonSerialize()
            ]);

        } catch (AuthException $e) {
            $this->jsonError(403, $e->getMessage());
        } catch (TaskException $e) {
            $this->jsonError(400, $e->getMessage());
        } catch (\Exception $e) {
            $this->jsonError(500, 'An unexpected error occurred');
            error_log("Task creation error: " . $e->getMessage());
        }
    }

    public function handleEdit(): void
    {
        // Set JSON response header
        header('Content-Type: application/json');

        try {
            // Verify CSRF token
            Csrf::requireVerification($_POST['csrf'] ?? null);

            // Get current user ID from session
            $userId = $this->getCurrentUserId();
            if (!$userId) {
                $this->jsonError(401, 'Not authenticated');
                return;
            }

            // Extract and validate form data
            $data = $this->extractTaskDataFromPost();

            // Validate required fields
            if (!$data['taskId']) {
                $this->jsonError(400, 'Invalid task ID');
                return;
            }

            if (!$data['projectId']) {
                $this->jsonError(400, 'Invalid project ID');
                return;
            }

            if (empty($data['title'])) {
                $this->jsonError(400, 'Title is required');
                return;
            }

            // SECURITY: Validate user still has access to the project (prevents removed users from editing tasks)
            $this->requireProjectAccess($data['projectId'], AccessRole::Member);

            // Update task through service
            $task = $this->taskService->updateTask(
                $data['taskId'],
                $data['title'],
                $data['description'] ?: null,
                $data['status'],
                $data['priority'],
                $data['assigneeId'] ?: null,
                $data['dueDate']
            );

            // Return success response with updated task data
            $this->jsonSuccess(200, [
                'message' => 'Task updated successfully',
                'task' => $task->jsonSerialize()
            ]);

        } catch (AuthException $e) {
            $this->jsonError(403, $e->getMessage());
        } catch (TaskException $e) {
            $this->jsonError(400, $e->getMessage());
        } catch (\Exception $e) {
            $this->jsonError(500, 'An unexpected error occurred');
            error_log("Task update error: " . $e->getMessage());
        }
    }

    public function handleDeletion(): void
    {
        // Set JSON response header
        header('Content-Type: application/json');

        try {
            // Verify CSRF token
            Csrf::requireVerification($_POST['csrf'] ?? null);

            // Get current user ID from session
            $userId = $this->getCurrentUserId();
            if (!$userId) {
                $this->jsonError(401, 'Not authenticated');
                return;
            }

            // Extract and validate form data
            $data = $this->extractTaskDataFromPost();

            // Validate required fields
            if (!$data['taskId']) {
                $this->jsonError(400, 'Invalid task ID');
                return;
            }

            if (!$data['projectId']) {
                $this->jsonError(400, 'Invalid project ID');
                return;
            }

            // SECURITY: Validate user still has access to the project (prevents removed users from deleting tasks)
            $this->requireProjectAccess($data['projectId'], AccessRole::Member);

            // Delete task through service
            $this->taskService->deleteTask($data['taskId']);

            // Return success response
            $this->jsonSuccess(200, [
                'message' => 'Task deleted successfully'
            ]);

        } catch (AuthException $e) {
            $this->jsonError(403, $e->getMessage());
        } catch (TaskException $e) {
            $this->jsonError(400, $e->getMessage());
        } catch (Exception $e) {
            $this->jsonError(500, 'An unexpected error occurred');
            error_log("Task deletion error: " . $e->getMessage());
        }
    }

    /**
     * Extract and sanitize task-related data from POST request
     *
     * @return array Associative array with sanitized task data
     */
    private function extractTaskDataFromPost(): array
    {
        return [
            'taskId' => filter_var($_POST['task_id'] ?? 0, FILTER_VALIDATE_INT),
            'projectId' => filter_var($_POST['project_id'] ?? 0, FILTER_VALIDATE_INT),
            'title' => trim($_POST['title'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'status' => $_POST['status'] ?? '',
            'priority' => $_POST['priority'] ?? '',
            'assigneeId' => filter_var($_POST['assignee'] ?? null, FILTER_VALIDATE_INT),
            'dueDate' => $_POST['due_date'] ?? '',
        ];
    }
}