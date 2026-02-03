<?php

namespace App\Repositories\Interfaces;

use App\Models\Enums\TaskPriority;
use App\Models\Enums\TaskStatus;
use App\Models\Task;
use DateTimeImmutable;

interface TaskRepositoryInterface
{
    // TODO: Maybe return a DTO array instead of Model

    /** Retrieves all tasks for a given project.
     * @return Task[]*/
    public function getAllProjectTasks(int $projectId): array;

    /** Creates a new task in the database.
     * @return Task the created task object*/
    public function createTask(
        int $projectId,
        string $title,
        ?string $description,
        TaskStatus $status,
        TaskPriority $priority,
        int $creatorId,
        ?int $assigneeId,
        DateTimeImmutable $dueDate
    ): Task;

    /** Changes the status (column) of a task.
     * @return bool true on success, false on failure*/
    public function changeTaskStatus(int $taskId, string $newColumn): bool;
}