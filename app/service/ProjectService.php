<?php

namespace App\Service;

use App\Model\ProjectModel;
use App\Repository\ProjectRepository;

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

    public function getProjectByProjectId($projectId) : ?ProjectModel
    {
        return $this->projectRepository->getProjectByProjectId($projectId);
    }
}

?>