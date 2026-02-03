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

    public function createTask(
        int $projectId,
        string $title,
        ?string $description,
        string $status,
        string $priority,
        int $creatorId,
        ?int $assigneeId,
        string $dueDate
    ): Task
    {
        // Validate title
        $title = trim($title);
        if (empty($title)) {
            throw new TaskException("Task title is required");
        }
        if (strlen($title) < 3 || strlen($title) > 256) {
            throw new TaskException("Task title must be between 3 and 256 characters");
        }

        // Validate description
        $description = trim($description ?? '');
        if (strlen($description) > 1000) {
            throw new TaskException("Task description must not exceed 1000 characters");
        }
        $description = empty($description) ? null : $description;

        // Validate status enum
        $statusEnum = TaskStatus::tryFrom($status);
        if (!$statusEnum) {
            throw new TaskException("Invalid task status");
        }

        // Validate priority enum
        $priorityEnum = TaskPriority::tryFrom($priority);
        if (!$priorityEnum) {
            throw new TaskException("Invalid task priority");
        }

        // Validate and parse due date
        try {
            $dueDateObj = new DateTimeImmutable($dueDate);
        } catch (\Exception $e) {
            throw new TaskException("Invalid due date format");
        }

        // Validate assignee (if provided, it should be a valid user ID > 0)
        if ($assigneeId !== null && $assigneeId <= 0) {
            throw new TaskException("Invalid assignee ID");
        }

        try {
            return $this->taskRepo->createTask(
                $projectId,
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

    public function updateTask(
        int $taskId,
        string $title,
        ?string $description,
        string $status,
        string $priority,
        ?int $assigneeId,
        string $dueDate
    ): Task
    {
        // Validate title
        $title = trim($title);
        if (empty($title)) {
            throw new TaskException("Task title is required");
        }
        if (strlen($title) < 3 || strlen($title) > 256) {
            throw new TaskException("Task title must be between 3 and 256 characters");
        }

        // Validate description
        $description = trim($description ?? '');
        if (strlen($description) > 1000) {
            throw new TaskException("Task description must not exceed 1000 characters");
        }
        $description = empty($description) ? null : $description;

        // Validate status enum
        $statusEnum = TaskStatus::tryFrom($status);
        if (!$statusEnum) {
            throw new TaskException("Invalid task status");
        }

        // Validate priority enum
        $priorityEnum = TaskPriority::tryFrom($priority);
        if (!$priorityEnum) {
            throw new TaskException("Invalid task priority");
        }

        // Validate and parse due date
        try {
            $dueDateObj = new DateTimeImmutable($dueDate);
        } catch (\Exception $e) {
            throw new TaskException("Invalid due date format");
        }

        // Validate assignee (if provided, it should be a valid user ID > 0)
        if ($assigneeId !== null && $assigneeId <= 0) {
            throw new TaskException("Invalid assignee ID");
        }

        try {
            return $this->taskRepo->updateTask(
                $taskId,
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