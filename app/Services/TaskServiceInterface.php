<?php

namespace App\Services;

interface TaskServiceInterface
{
    public function getAllColumnTasks(int $projectId): array;
    public function moveTaskToColumn(int $taskId, string $newColumn): bool;
}