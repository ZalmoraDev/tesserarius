<?php

namespace App\Services;

use App\Models\Project;
use App\Repositories\ProjectBaseRepository;

final class ProjectService
{
    private ProjectBaseRepository $projectRepository;

    function __construct($projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function getProjectsByUserAndRole($userId, $role) : array
    {
        // Role = admin OR member
        // TODO: Implement existing enum
        return $this->projectRepository->getProjectsByUserAndRole($userId, $role);
    }

    public function getProjectByProjectId($projectId) : ?Project
    {
        return $this->projectRepository->getProjectByProjectId($projectId);
    }
}

?>