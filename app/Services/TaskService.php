<?php

namespace App\Services;

use App\Repositories\TaskRepository;

class TaskService
{
    private TaskRepository $taskRepository;

    function __construct($taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function getAllColumnTasks(int $projectId): array
    {
        // column = backlog, to-do, doing, review, done
        // TODO: Implement existing TaskColumn.php enum
        return $this->taskRepository->getAllColumnTasks($projectId);
    }

    public function moveTaskToColumn(int $taskId, string $newColumn): bool
    {
        return $this->taskRepository->moveTaskToColumn($taskId, $newColumn);
    }
}