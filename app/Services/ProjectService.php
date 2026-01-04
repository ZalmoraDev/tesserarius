<?php

namespace App\Services;

use App\Models\Project;
use App\Repositories\ProjectRepository;

class ProjectService
{
    private ProjectRepository $projectRepository;

    function __construct()
    {
        $this->projectRepository = new ProjectRepository();
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