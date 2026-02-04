<?php

namespace App\Services;

use App\Models\Task;
use App\Repositories\Exceptions\TaskRepoException;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\Services\Exceptions\TaskException;
use App\Services\Interfaces\TaskServiceInterface;

final readonly class TaskService implements TaskServiceInterface
{
    private TaskRepositoryInterface $taskRepo;

    function __construct(TaskRepositoryInterface $taskRepo)
    {
        $this->taskRepo = $taskRepo;
    }

    /** Retrieves all tasks for a given project ID,
     * which get divided into subarrays of 'status' => 'tasks' in projectView.php
     * @return array<int, Task[]>
     */
    public function getAllProjectTasks(int $projectId): array
    {
        return $this->taskRepo->getAllProjectTasks($projectId);
    }

    /** Creates a new task after validating input data */
    public function createTask(Task $task, int $creatorId): Task
    {
        if ($task->projectId <= 0)
            throw new TaskException(TaskException::INVALID_PROJECT_ID);

        $this->validateInput($task);

        try {
            // Pass the Task object to repository, letting repository handle adding/updating fields
            return $this->taskRepo->createTask($task, $creatorId);
        } catch (TaskRepoException $e) {
            throw new TaskException(TaskException::CREATION_FAILED . $e->getMessage());
        }
    }

    /** Updates an existing task after validating input data */
    public function updateTask(Task $task): Task
    {
        if ($task->id <= 0)
            throw new TaskException(TaskException::INVALID_TASK_ID);

        $this->validateInput($task);

        try {
            // Pass the Task object to repository, letting repository handle adding/updating fields
            return $this->taskRepo->updateTask($task);
        } catch (TaskRepoException $e) {
            throw new TaskException(TaskException::UPDATE_FAILED . $e->getMessage());
        }
    }

    /** Deletes a task by its ID */
    public function deleteTask(int $taskId): void
    {
        if ($taskId <= 0)
            throw new TaskException(TaskException::INVALID_TASK_ID);

        try {
            $success = $this->taskRepo->deleteTask($taskId);
            if (!$success)
                throw new TaskException(TaskException::DELETION_FAILED);
        } catch (TaskRepoException $e) {
            throw new TaskException(TaskException::DELETION_FAILED_WITH_REASON . $e->getMessage());
        }
    }

    /** Validates task input data */
    private function validateInput(Task $task): void
    {
        // Validate title
        $title = trim($task->title);
        if (empty($title))
            throw new TaskException(TaskException::TITLE_REQUIRED);
        if (strlen($title) <= 3 || strlen($title) >= 256)
            throw new TaskException(TaskException::TITLE_LENGTH_INVALID);

        // Validate description
        $description = trim($task->description ?? '');
        if (strlen($description) > 512)
            throw new TaskException(TaskException::DESCRIPTION_TOO_LONG);

        // Validate assignee (if provided, it should be a valid user ID > 0)
        if (is_int($task->assigneeId) && $task->assigneeId <= 0)
            throw new TaskException(TaskException::INVALID_ASSIGNEE_ID);
    }
}