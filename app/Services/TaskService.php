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

    public function getAllColumnTasks(int $projectId): array
    {
        // column = backlog, to-do, doing, review, done
        // TODO: Implement existing TaskColumn.php enum
        return $this->taskRepo->getAllColumnTasks($projectId);
    }

    public function moveTaskToColumn(int $taskId, string $newColumn): bool
    {
        return $this->taskRepo->moveTaskToColumn($taskId, $newColumn);
    }
}