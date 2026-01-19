<?php

namespace App\Services;

use App\Models\Enums\UserRole;
use App\Models\ProjectInvite;
use App\Repositories\ProjectMembersRepositoryInterface;
use App\Services\Exceptions\ProjectMembersException;
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

    public function generateProjectInviteCodes(int $projectId, DateTimeImmutable $expiresAt, int $count): void
    {
        $now = new DateTimeImmutable(); // Gets set in database to current time
        $createdBy = $_SESSION['auth']['username'];
        $invites = [];

        // id is temporarily 0, since it will be auto-generated and replaced by the database
        // Generates $count amount of invite codes to be created in the database
        for ($i = 0; $i < $count; $i++)
            $invites[] = new ProjectInvite(0, $projectId, $this->generateInviteCode(
                16), $expiresAt, null, $createdBy, $now);

        $this->projectMembersRepo->createProjectInviteCodes($invites);
        // TODO: Error handling
    }

    public function removeProjectInviteCode(int $inviteId): void
    {
        $success = $this->projectMembersRepo->removeProjectInviteCode($inviteId);
        if (!$success)
            throw new ProjectMembersException(ProjectMembersException::INVITE_REMOVAL_FAILED);
    }

    public function joinProjectByInviteCode(int $userId, string $inviteCode): bool
    {
        //$this->projectMembersRepo->addProjectMember($projectId, (int)$_SESSION['auth']['userId'], UserRole::Member);
        return false;
    }

    public function removeProjectMember(int $projectId, int $userId): bool
    {
        // TODO: Implement removeProjectMember() method.
        return false;
    }

    /** Generates a random invite code of specified length. (16 characters) */
    private function generateInviteCode(int $length): string
    {
        $symbols = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $max = strlen($symbols) - 1;

        $code = '';
        for ($i = 0; $i < $length; $i++)
            $code .= $symbols[random_int(0, $max)];
        return $code;
    }
}