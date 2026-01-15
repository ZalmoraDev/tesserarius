<?php

namespace App\Services;

use App\Models\Project;

interface ProjectServiceInterface
{
    public function getDashboardProjects(int $userId): array;
    public function getProjectByProjectId(int $projectId): ?Project;
}