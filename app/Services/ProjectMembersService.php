<?php

namespace App\Services;

use App\Repositories\ProjectMembersRepositoryInterface;
use DateTimeImmutable;

final class ProjectMembersService implements ProjectMembersServiceInterface
{
    private ProjectMembersRepositoryInterface $projectMembersRepo;

    function __construct(ProjectMembersRepositoryInterface $projectMembersRepo)
    {
        $this->projectMembersRepo = $projectMembersRepo;
    }

    public function getProjectMembersByProjectId(int $projectId): ?array
    {
        return $this->projectMembersRepo->findProjectMembersByProjectId($projectId);
    }

    public function getProjectInviteCodes(int $projectId): ?array
    {
        return $this->projectMembersRepo->findProjectInviteCodes($projectId);
    }

    public function generateProjectInviteCode(int $projectId, DateTimeImmutable $expiresAt, int $count): bool
    {
        //$this->projectMembersRepo->createProjectInviteCodes($projectId, $expiresAt, $count);
        // TODO: Implement generateProjectInviteCode() method.
        return false;
    }

    public function joinProjectByInviteCode(int $userId, string $inviteCode): bool
    {
        // TODO: Implement joinProjectByInviteCode() method.
        return false;
    }

    public function removeProjectMember(int $projectId, int $userId): bool
    {
        // TODO: Implement removeProjectMember() method.
        return false;
    }


}