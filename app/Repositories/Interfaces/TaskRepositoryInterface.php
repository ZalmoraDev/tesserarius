<?php

namespace App\Repositories\Interfaces;

use App\Models\Enums\TaskPriority;
use App\Models\Enums\TaskStatus;
use App\Models\Task;
use App\Repositories\Exceptions\TaskRepoException;
use DateMalformedStringException;
use DateTimeImmutable;

interface TaskRepositoryInterface
{
    /** Retrieves all tasks for a given project.
     * @return Task[]
     * @throws TaskRepoException if database query fails
     * @throws DateMalformedStringException if date parsing fails
     */
    public function getAllProjectTasks(int $projectId): array;

    /** Creates a new task in the database.
     * @param Task $task Task object with form data
     * @param int $creatorId ID of the user creating the task
     * @return Task the created task object with DB-generated fields (id, createdAt)
     * @throws TaskRepoException if database operation fails
     * @throws DateMalformedStringException if date parsing fails
     */
    public function createTask(Task $task, int $creatorId): Task;

    /** Changes the status of a task.
     * @throws TaskRepoException if task not found or database operation fails
     */
    public function changeTaskStatus(int $taskId, string $newStatus): void;

    /** Updates a task in the database.
     * @param Task $task Task object with updated data
     * @return Task the updated task object (same as input since readonly)
     * @throws TaskRepoException if task not found or database operation fails
     */
    public function updateTask(Task $task): Task;

    /** Deletes a task from the database.
     * @throws TaskRepoException if task not found or database operation fails
     */
    public function deleteTask(int $taskId): void;
}