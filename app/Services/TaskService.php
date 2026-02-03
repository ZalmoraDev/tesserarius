<?php

namespace App\Services;

use App\Models\Task;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\Services\Interfaces\TaskServiceInterface;

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

    public function createTask(string $title, string $description, int $projectId, ?int $assigneeId): Task
    {
        // TODO: Implement createTask() method.
    }

    public function editTask(int $taskId, string $newColumn): bool
    {
        return $this->taskRepo->changeTaskStatus($taskId, $newColumn);
    }
    
    public function deleteTask(int $taskId): void
    {
        // TODO: Implement deleteTask() method.
    }
}