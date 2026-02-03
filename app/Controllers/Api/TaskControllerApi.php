<?php

namespace App\Controllers\Api;

use App\Core\Csrf;
use App\Services\Exceptions\TaskException;
use App\Services\Interfaces\TaskServiceInterface;

class TaskControllerApi
{
    private TaskServiceInterface $taskService;

    public function __construct(TaskServiceInterface $taskService)
    {
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
            $userId = $_SESSION['auth']['userId'] ?? null;
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'error' => 'Not authenticated']);
                return;
            }

            // Extract and validate form data
            $projectId = filter_var($_POST['project_id'] ?? 0, FILTER_VALIDATE_INT);
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $status = $_POST['status'] ?? '';
            $priority = $_POST['priority'] ?? '';
            $assigneeId = filter_var($_POST['assignee'] ?? null, FILTER_VALIDATE_INT);
            $dueDate = $_POST['due_date'] ?? '';

            // Validate required fields
            if (!$projectId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid project ID']);
                return;
            }

            if (empty($title)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Title is required']);
                return;
            }

            if (empty($dueDate)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Due date is required']);
                return;
            }

            // Create task through service
            $task = $this->taskService->createTask(
                $projectId,
                $title,
                $description ?: null,
                $status,
                $priority,
                $userId,
                $assigneeId ?: null,
                $dueDate
            );

            // Return success response with created task data
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Task created successfully',
                'task' => $task->jsonSerialize()
            ]);

        } catch (TaskException $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'An unexpected error occurred']);
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
            $userId = $_SESSION['auth']['userId'] ?? null;
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'error' => 'Not authenticated']);
                return;
            }

            // Extract and validate form data
            $taskId = filter_var($_POST['task_id'] ?? 0, FILTER_VALIDATE_INT);
            $projectId = filter_var($_POST['project_id'] ?? 0, FILTER_VALIDATE_INT);
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $status = $_POST['status'] ?? '';
            $priority = $_POST['priority'] ?? '';
            $assigneeId = filter_var($_POST['assignee'] ?? null, FILTER_VALIDATE_INT);
            $dueDate = $_POST['due_date'] ?? '';

            // Validate required fields
            if (!$taskId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid task ID']);
                return;
            }

            if (!$projectId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid project ID']);
                return;
            }

            if (empty($title)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Title is required']);
                return;
            }

            if (empty($dueDate)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Due date is required']);
                return;
            }

            // Update task through service
            $task = $this->taskService->updateTask(
                $taskId,
                $title,
                $description ?: null,
                $status,
                $priority,
                $assigneeId ?: null,
                $dueDate
            );

            // Return success response with updated task data
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Task updated successfully',
                'task' => $task->jsonSerialize()
            ]);

        } catch (TaskException $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'An unexpected error occurred']);
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
            $userId = $_SESSION['auth']['userId'] ?? null;
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'error' => 'Not authenticated']);
                return;
            }

            // Extract and validate form data
            $taskId = filter_var($_POST['task_id'] ?? 0, FILTER_VALIDATE_INT);
            $projectId = filter_var($_POST['project_id'] ?? 0, FILTER_VALIDATE_INT);

            // Validate required fields
            if (!$taskId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid task ID']);
                return;
            }

            if (!$projectId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid project ID']);
                return;
            }

            // Delete task through service
            $this->taskService->deleteTask($taskId);

            // Return success response
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Task deleted successfully'
            ]);

        } catch (TaskException $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'An unexpected error occurred']);
            error_log("Task deletion error: " . $e->getMessage());
        }
    }
}