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

    public function getDashboardProjects(int $userId): array
    {
        $projects = $this->projectRepo->getProjectListItemsByUserId($userId);

        // Owned = owner | Member = Admin + Member
        $owned = [];
        $member = [];
        foreach ($projects as $project) {
            if ($project->role === 'Owner')
                $owned[] = $project;
            else
                $member[] = $project;
        }

        return [
            'owned' => $owned,
            'member' => $member,
        ];
    }

    public function getProjectByProjectId(int $projectId): ?Project
    {
        return $this->projectRepo->getProjectByProjectId($projectId);
    }
}