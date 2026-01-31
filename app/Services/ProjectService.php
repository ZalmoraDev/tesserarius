<?php

namespace App\Services;

use App\Models\Enums\UserRole;
use App\Models\Project;
use App\Repositories\Interfaces\ProjectMembersRepositoryInterface;
use App\Repositories\Interfaces\ProjectRepositoryInterface;
use App\Services\Exceptions\ProjectException;
use App\Services\Interfaces\ProjectServiceInterface;

final readonly class ProjectService implements ProjectServiceInterface
{
    private ProjectRepositoryInterface $projectRepo;
    private ProjectMembersRepositoryInterface $projectMembersRepo;

    function __construct(ProjectRepositoryInterface $projectRepo, ProjectMembersRepositoryInterface $projectMembersRepo)
    {
        $this->projectRepo = $projectRepo;
        $this->projectMembersRepo = $projectMembersRepo;
    }

    //region Retrieve
    public function getProjectByProjectId(int $projectId): Project
    {
        $project = $this->projectRepo->findProjectByProjectId($projectId);
        if ($project === null)
            throw new ProjectException(ProjectException::PROJECT_NOT_FOUND);

        return $project;
    }

    public function getHomeProjects(int $userId): array
    {
        $projects = $this->projectRepo->findProjectListItemsByUserId($userId);

        $owned = [];  // Owner
        $member = []; // Admin + Member
        foreach ($projects as $project)
            if ($project->userRole === UserRole::Owner)
                $owned[] = $project;
            else
                $member[] = $project;

        return [
            'owned' => $owned, // Your projects
            'member' => $member,
        ];
    }
    //endregion


    //region Modify
    public function createProject(string $name, string $description): int
    {
        $name = trim($name);
        $description = trim($description);

        // name/description do not meet format requirements
        if (!preg_match('/^.{3,32}$/', $name))
            throw new ProjectException(ProjectException::NAME_INVALID);
        if (!preg_match('/^.{0,128}$/', $description))
            throw new ProjectException(ProjectException::DESCRIPTION_INVALID);

        // username already has a project by this name
        if ($this->projectRepo->existsByName($name))
            throw new ProjectException(ProjectException::NAME_TAKEN);

        // failed attempt creating the new project
        $ownerId = (int)$_SESSION['auth']['userId'];
        $newProjectId = $this->projectRepo->createProject($ownerId, $name, $description);
        if ($newProjectId === null)
            throw new ProjectException(ProjectException::REGISTRATION_FAILED);

        // If no exceptions were thrown, meaning the project was created successfully
        // -> add this user as 'Owner' to 'project_members' DB table
        $this->projectMembersRepo->addProjectMember($newProjectId, (int)$_SESSION['auth']['userId'], UserRole::Owner);
        return $newProjectId;
    }

    public function editProject(int $projectId, string $name, string $description): void
    {
        $name = trim($name);
        $description = trim($description);

        // name/description do not meet format requirements
        if (!preg_match('/^.{3,32}$/', $name))
            throw new ProjectException(ProjectException::NAME_INVALID);
        if (!preg_match('/^.{0,128}$/', $description))
            throw new ProjectException(ProjectException::DESCRIPTION_INVALID);

        // username already has a project by this name
        if ($this->projectRepo->existsByName($name))
            throw new ProjectException(ProjectException::NAME_TAKEN);

        // failed attempt editing the project
        $success = $this->projectRepo->editProject($projectId, $name, $description);
        if (!$success)
            throw new ProjectException(ProjectException::EDIT_FAILED);
    }

    public function deleteProject(int $projectId, string $confirmName): void
    {
        // We could fetch the project, and it's name through the view,
        // but to avoid further 'spaghettification', we re-fetch it
        $projectName = $this->projectRepo->findProjectNameByProjectId($projectId);

        if ($projectName === null)
            throw new ProjectException(ProjectException::PROJECT_NOT_FOUND);
        if ($confirmName !== $projectName)
            throw new ProjectException(ProjectException::DELETION_NAME_MISMATCH);

        // failed attempt deleting the project
        $success = $this->projectRepo->deleteProject($projectId);
        if (!$success)
            throw new ProjectException(ProjectException::DELETION_FAILED);
    }
    //endregion
}