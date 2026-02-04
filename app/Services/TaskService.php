<?php

namespace App\Services;

use App\Models\Enums\TaskPriority;
use App\Models\Enums\TaskStatus;
use App\Models\Task;
use App\Repositories\Exceptions\TaskRepositoryException;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\Services\Exceptions\TaskException;
use App\Services\Interfaces\TaskServiceInterface;
use DateTimeImmutable;

final readonly class TaskService implements TaskServiceInterface
{
    private TaskRepositoryInterface $taskRepo;

    function __construct(TaskRepositoryInterface $taskRepo)
    {
        $this->taskRepo = $taskRepo;
    }

    public function getAllProjectTasks(int $projectId): array
    {
        return $this->taskRepo->getAllProjectTasks($projectId);
    }

    public function createTask(Task $task, int $creatorId): Task
    {
        // Validate project ID
        if ($task->projectId <= 0) {
            throw new TaskException("Invalid project ID");
        }

        // Validate title
        $title = trim($task->title);
        if (empty($title)) {
            throw new TaskException("Title is required");
        }
        if (strlen($title) < 3 || strlen($title) > 256) {
            throw new TaskException("Task title must be between 3 and 256 characters");
        }

        // Validate description
        $description = trim($task->description ?? '');
        if (strlen($description) > 1000) {
            throw new TaskException("Task description must not exceed 1000 characters");
        }

        // Validate assignee (if provided, it should be a valid user ID > 0)
        if (is_int($task->assigneeId) && $task->assigneeId <= 0) {
            throw new TaskException("Invalid assignee ID");
        }

        try {
            // Pass the Task object directly to repository
            return $this->taskRepo->createTask($task, $creatorId);
        } catch (TaskRepositoryException $e) {
            throw new TaskException("Failed to create task: " . $e->getMessage());
        }
    }

    public function updateTask(Task $task): Task
    {
        // Validate task ID
        if ($task->id <= 0) {
            throw new TaskException("Invalid task ID");
        }

        // Validate title
        $title = trim($task->title);
        if (empty($title)) {
            throw new TaskException("Title is required");
        }
        if (strlen($title) < 3 || strlen($title) > 256) {
            throw new TaskException("Task title must be between 3 and 256 characters");
        }

        // Validate description
        $description = trim($task->description ?? '');
        if (strlen($description) > 1000) {
            throw new TaskException("Task description must not exceed 1000 characters");
        }

        // Validate assignee (if provided, it should be a valid user ID > 0)
        if (is_int($task->assigneeId) && $task->assigneeId <= 0) {
            throw new TaskException("Invalid assignee ID");
        }

        try {
            // Pass the Task object directly to repository
            return $this->taskRepo->updateTask($task);
        } catch (TaskRepositoryException $e) {
            throw new TaskException("Failed to update task: " . $e->getMessage());
        }
    }

    public function deleteTask(int $taskId): void
    {
        // Validate task ID
        if ($taskId <= 0) {
            throw new TaskException("Invalid task ID");
        }

        try {
            $success = $this->taskRepo->deleteTask($taskId);
            if (!$success) {
                throw new TaskException("Failed to delete task");
            }
        } catch (TaskRepositoryException $e) {
            throw new TaskException("Failed to delete task: " . $e->getMessage());
        }
    }
}