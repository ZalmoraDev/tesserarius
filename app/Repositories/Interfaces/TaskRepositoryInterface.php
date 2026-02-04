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
     * @param Task $task Task object with form data
     * @param int $creatorId ID of the user creating the task
     * @return Task the created task object with DB-generated fields (id, createdAt)
     */
    public function createTask(Task $task, int $creatorId): Task;

    /** Changes the status of a task.
     * @return bool true on success, false on failure*/
    public function changeTaskStatus(int $taskId, string $newStatus): bool;

    /** Updates a task in the database.
     * @param Task $task Task object with updated data
     * @return Task the updated task object (same as input since readonly)
     */
    public function updateTask(Task $task): Task;

    /** Deletes a task from the database.
     * @return bool true on success, false on failure*/
    public function deleteTask(int $taskId): bool;
}