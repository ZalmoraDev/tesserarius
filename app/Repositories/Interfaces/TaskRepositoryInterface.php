<?php

namespace App\Repositories\Interfaces;

use App\Models\Task;

interface TaskRepositoryInterface
{
    // TODO: Maybe return a DTO array instead of Model

    /** Retrieves all tasks for a given project.
     * @return Task[]*/
    public function getAllProjectTasks(int $projectId): array;

    /** Changes the status (column) of a task.
     * @return bool true on success, false on failure*/
    public function changeTaskStatus(int $taskId, string $newColumn): bool;
}