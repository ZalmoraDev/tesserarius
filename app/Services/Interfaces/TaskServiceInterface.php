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

    /** Creates a new task.
     * @return Task the created task
     * @throws TaskException on validation failure or database error
     */
    public function createTask(
        int $projectId,
        string $title,
        ?string $description,
        string $status,
        string $priority,
        int $creatorId,
        ?int $assigneeId,
        string $dueDate
    ): Task;

    /** Changes the status (column) of a task.
     * @return bool true on success, false on failure
     * @throws TaskException on failure
     */
    public function editTask(int $taskId, string $newColumn): bool;
    public function deleteTask(int $taskId): void;

}