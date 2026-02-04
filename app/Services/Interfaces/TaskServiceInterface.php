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
     * @param Task $task Task model with form data
     * @param int $creatorId ID of the user creating the task
     * @return Task the created task
     * @throws TaskException on validation failure or database error
     */
    public function createTask(Task $task, int $creatorId): Task;

    /** Changes the status of a task.
     * @return bool true on success, false on failure
     * @throws TaskException on failure
     */

    /** Updates a task.
     * @param Task $task Task model with updated form data
     * @return Task the updated task
     * @throws TaskException on validation failure or database error
     */
    public function updateTask(Task $task): Task;

    /** Deletes a task.
     * @throws TaskException on failure
     */
    public function deleteTask(int $taskId): void;

}