<?php

namespace App\Services\Interfaces;

use App\Models\Task;
use App\Services\Exceptions\TaskException;

interface TaskServiceInterface
{
    // TODO: Add throw annotations when exceptions are implemented
    // TODO: Maybe return a DTO array insead of Model

    /** Retrieves all tasks for a given project.
     * @return Task[] for all tasks in the project
     * @throws TaskException on failure
     */
    public function getAllProjectTasks(int $projectId): array;

    /** Changes the status (column) of a task.
     * @return bool true on success, false on failure
     * @throws TaskException on failure
     */
    public function changeTaskStatus(int $taskId, string $newColumn): bool;
}