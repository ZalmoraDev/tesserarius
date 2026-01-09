<?php

namespace App\Repositories;

interface TaskRepositoryInterface
{
    public function getAllColumnTasks(int $projectId): array;
    public function moveTaskToColumn(int $taskId, string $newColumn): bool;
}