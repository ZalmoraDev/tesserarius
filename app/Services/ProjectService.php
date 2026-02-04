<?php

namespace App\Services;

use App\Models\Enums\UserRole;
use App\Models\Project;
use App\Repositories\Interfaces\ProjectMembersRepositoryInterface;
use App\Repositories\Interfaces\ProjectRepositoryInterface;
use App\Services\Exceptions\ProjectException;
use App\Services\Exceptions\ServiceException;
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
        $project = ServiceException::handleRepoCall(
            fn() => $this->projectRepo->findProjectByProjectId($projectId),
            ProjectException::class,
            __FUNCTION__
        );

        if ($project === null)
            throw new ProjectException(ProjectException::PROJECT_NOT_FOUND);

        return $project;
    }

    public function getHomeProjects(int $userId): array
    {
        $projects = ServiceException::handleRepoCall(
            fn() => $this->projectRepo->findProjectListItemsByUserId($userId),
            ProjectException::class,
            __FUNCTION__
        );
        // Having no projects is not an error, so we don't check for null

        $owned = [];  // Owner
        $member = []; // Admin + Member
        foreach ($projects as $project)
            if ($project->userRole === UserRole::Owner)
                $owned[] = $project;
            else
                $member[] = $project;

        return [
            'owned' => $owned,
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
        $nameExists = ServiceException::handleRepoCall(
            fn() => $this->projectRepo->existsByName($name),
            ProjectException::class,
            __FUNCTION__
        );
        if ($nameExists)
            throw new ProjectException(ProjectException::NAME_TAKEN);

        // Create the new project
        $ownerId = (int)$_SESSION['auth']['userId'];
        $newProjectId = ServiceException::handleRepoCall(
            fn() => $this->projectRepo->createProject($ownerId, $name, $description),
            ProjectException::class,
            __FUNCTION__
        );

        // Add this user as 'Owner' to the new project
        ServiceException::handleRepoCall(
            fn() => $this->projectMembersRepo->addProjectMember($newProjectId, (int)$_SESSION['auth']['userId'], UserRole::Owner),
            ProjectException::class,
            __FUNCTION__
        );

        return $newProjectId;
    }

    public function editProject(int $projectId, string $name, string $description, string $currentName): void
    {
        $name = trim($name);
        $description = trim($description);

        // name/description do not meet format requirements
        if (!preg_match('/^.{3,32}$/', $name))
            throw new ProjectException(ProjectException::NAME_INVALID);
        if (!preg_match('/^.{0,128}$/', $description))
            throw new ProjectException(ProjectException::DESCRIPTION_INVALID);

        // Only check if name is taken when the name is actually changing
        if ($name !== $currentName) {
            $nameExists = ServiceException::handleRepoCall(
                fn() => $this->projectRepo->existsByName($name),
                ProjectException::class,
                __FUNCTION__
            );
            if ($nameExists)
                throw new ProjectException(ProjectException::NAME_TAKEN);
        }

        // Update the project
        ServiceException::handleRepoCall(
            fn() => $this->projectRepo->editProject($projectId, $name, $description),
            ProjectException::class,
            __FUNCTION__
        );
    }

    public function deleteProject(int $projectId, string $confirmName): void
    {
        // We could fetch the project, and it's name through the view,
        // but to avoid further 'spaghettification', we re-fetch it
        $projectName = ServiceException::handleRepoCall(
            fn() => $this->projectRepo->findProjectNameByProjectId($projectId),
            ProjectException::class,
            __FUNCTION__
        );

        if ($projectName === null)
            throw new ProjectException(ProjectException::PROJECT_NOT_FOUND);
        if ($confirmName !== $projectName)
            throw new ProjectException(ProjectException::DELETION_NAME_MISMATCH);

        // Delete the project
        ServiceException::handleRepoCall(
            fn() => $this->projectRepo->deleteProject($projectId),
            ProjectException::class,
            __FUNCTION__
        );
    }
    //endregion
}