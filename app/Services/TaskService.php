<?php

namespace App\Services;

use App\Models\Task;
use App\Services\Exceptions\ServiceException;
use App\Services\Exceptions\TaskException;
use App\Services\Interfaces\TaskServiceInterface;
use App\Repositories\Interfaces\TaskRepositoryInterface;

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
        return ServiceException::handleRepoCall(
            fn() => $this->taskRepo->getAllProjectTasks($projectId),
            TaskException::class,
            __FUNCTION__
        );
    }

    /** Creates a new task after validating input data */
    public function createTask(Task $task, int $creatorId): Task
    {
        if ($task->projectId <= 0)
            throw new TaskException(TaskException::INVALID_PROJECT_ID);

        $this->validateInput($task);

        return ServiceException::handleRepoCall(
            fn() => $this->taskRepo->createTask($task, $creatorId),
            TaskException::class,
            __FUNCTION__
        );
    }

    /** Updates an existing task after validating input data */
    public function updateTask(Task $task): Task
    {
        if ($task->id <= 0)
            throw new TaskException(TaskException::INVALID_TASK_ID);

        $this->validateInput($task);

        return ServiceException::handleRepoCall(
            fn() => $this->taskRepo->updateTask($task),
            TaskException::class,
            __FUNCTION__
        );
    }

    /** Deletes a task by its ID */
    public function deleteTask(int $taskId): void
    {
        if ($taskId <= 0)
            throw new TaskException(TaskException::INVALID_TASK_ID);

        ServiceException::handleRepoCall(
            fn() => $this->taskRepo->deleteTask($taskId),
            TaskException::class,
            __FUNCTION__
        );
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