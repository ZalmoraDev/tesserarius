<?php

namespace App\Services;

use App\Models\Enums\UserRole;
use App\Models\Project;
use App\Repositories\ProjectRepositoryInterface;
use App\Services\Exceptions\ProjectException;

final class ProjectService implements ProjectServiceInterface
{
    private ProjectRepositoryInterface $projectRepo;

    function __construct(ProjectRepositoryInterface $projectRepo)
    {
        $this->projectRepo = $projectRepo;
    }

    /** Returns an array of projects owned by or shared with the specified user.
     * This is then split into 'Your' and 'Member' projects for display on the home page. */
    public function getHomeProjects(int $userId): array
    {
        $projects = $this->projectRepo->findProjectListItemsByUserId($userId);

        $owned = [];  // Owner
        $member = []; // Admin + Member
        foreach ($projects as $project) {
            if ($project->userRole === UserRole::Owner)
                $owned[] = $project;
            else
                $member[] = $project;
        }

        return [
            'owned' => $owned, // Your projects
            'member' => $member,
        ];
    }

    /** Creates a new project for the currently logged-in user.
     * Returns the new project's ID for the controller to redirect to the new project page. */
    public function createProject(string $name, string $description): ?int
    {
        $name = trim($name);
        $description = trim($description);

        // required fields are empty
        if (empty($name) || empty($description))
            throw new ProjectException(ProjectException::FIELDS_REQUIRED);

        // name/description do not meet format requirements
        if (!preg_match('/^.{3,32}$/', $name))
            throw new ProjectException(ProjectException::NAME_INVALID);
        if (!preg_match('/^.{0,128}$/', $description))
            throw new ProjectException(ProjectException::DESCRIPTION_INVALID);

        // username already has a project by this name
        if ($this->projectRepo->findProjectByName($name) !== null)
            throw new ProjectException(ProjectException::NAME_TAKEN);

        // failed attempt creating the new project
        $newProjectId = $this->projectRepo->createProject($name, $description);
        if ($newProjectId === null)
            throw new ProjectException(ProjectException::REGISTRATION_FAILED);

        // If no exceptions were thrown, then the project was created successfully
        // adding the owner as a member
        $this->projectRepo->addProjectMember((int)$newProjectId, (int)$_SESSION['auth']['userId'], UserRole::Owner);

        return $newProjectId;
    }

    public function getProjectByProjectId(int $projectId): ?Project
    {
        return $this->projectRepo->findProjectByProjectId($projectId);
    }


}