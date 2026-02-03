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
        $description = empty($description) ? null : $description;

        // Status and priority are already enums from the Task object
        $statusEnum = $task->status;
        $priorityEnum = $task->priority;

        // Due date is already DateTimeImmutable or null from the Task object
        $dueDateObj = $task->dueDate;

        // Validate assignee (if provided, it should be a valid user ID > 0)
        $assigneeId = $task->assigneeId;
        if (is_int($assigneeId) && $assigneeId <= 0) {
            throw new TaskException("Invalid assignee ID");
        }

        try {
            return $this->taskRepo->createTask(
                $task->projectId,
                $title,
                $description,
                $statusEnum,
                $priorityEnum,
                $creatorId,
                $assigneeId,
                $dueDateObj
            );
        } catch (TaskRepositoryException $e) {
            throw new TaskException("Failed to create task: " . $e->getMessage());
        }
    }

    public function editTask(int $taskId, string $newColumn): bool
    {
        return $this->taskRepo->changeTaskStatus($taskId, $newColumn);
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
        $description = empty($description) ? null : $description;

        // Status and priority are already enums from the Task object
        $statusEnum = $task->status;
        $priorityEnum = $task->priority;

        // Due date is already DateTimeImmutable or null from the Task object
        $dueDateObj = $task->dueDate;

        // Validate assignee (if provided, it should be a valid user ID > 0)
        $assigneeId = $task->assigneeId;
        if (is_int($assigneeId) && $assigneeId <= 0) {
            throw new TaskException("Invalid assignee ID");
        }

        try {
            return $this->taskRepo->updateTask(
                $task->id,
                $title,
                $description,
                $statusEnum,
                $priorityEnum,
                $assigneeId,
                $dueDateObj
            );
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