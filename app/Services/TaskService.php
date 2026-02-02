<?php

namespace App\Services;

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

    public function changeTaskStatus(int $taskId, string $newColumn): bool
    {
        return $this->taskRepo->changeTaskStatus($taskId, $newColumn);
    }
}