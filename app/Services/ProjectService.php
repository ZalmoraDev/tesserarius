<?php

namespace App\Services;

use App\Models\Project;
use App\Repositories\ProjectRepositoryInterface;

final class ProjectService implements ProjectServiceInterface
{
    private ProjectRepositoryInterface $projectRepo;

    function __construct(ProjectRepositoryInterface $projectRepo)
    {
        $this->projectRepo = $projectRepo;
    }

    public function getProjectsByUserAndRole(int $userId, string $role): array
    {
        return $this->projectRepo->getProjectsByUserAndRole($userId, $role);
    }

    public function getProjectByProjectId(int $projectId): ?Project
    {
        return $this->projectRepo->getProjectByProjectId($projectId);
    }
}