<?php

namespace App\Repositories\Interfaces;

interface TaskRepositoryInterface
{
    public function getAllColumnTasks(int $projectId): array;
    public function moveTaskToColumn(int $taskId, string $newColumn): bool;
}