<?php

namespace App\Repositories;

use App\Models\Project;

interface ProjectRepositoryInterface
{
    public function getProjectByProjectId(int $projectId): ?Project;

    public function getProjectListItemsByUserId(int $userId): array;
}