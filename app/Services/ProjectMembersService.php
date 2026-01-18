<?php

namespace App\Services;

use App\Models\Enums\UserRole;
use App\Models\Project;
use App\Repositories\ProjectMembersRepository;
use App\Repositories\ProjectMembersRepositoryInterface;
use App\Repositories\ProjectRepositoryInterface;
use App\Services\Exceptions\ProjectException;

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

    public function generateProjectInviteCode(int $projectId, DateTimeImmutable $expiresAt, int $count): ?string
    {
        $this->projectMembersRepo->generateProjectInviteCode($projectId, $expiresAt, $count);
        // TODO: Implement generateProjectInviteCode() method.
        return null;
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