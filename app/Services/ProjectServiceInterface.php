<?php

namespace App\Services;

use App\Models\Project;

interface ProjectServiceInterface
{
    public function getProjectsByUserAndRole(int $userId, string $role): array;
    public function getProjectByProjectId(int $projectId): ?Project;
}