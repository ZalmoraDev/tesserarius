<?php

namespace App\Services;

use DateTimeImmutable;

use App\Models\ProjectInvite;
use App\Services\Exceptions\ProjectMembersException;
use App\Services\Interfaces\ProjectMembersServiceInterface;
use App\Repositories\Exceptions\ProjectMembers\InviteCodeExpiredOrUsedException;
use App\Repositories\Exceptions\ProjectMembers\InviteNotFoundException;
use App\Repositories\Interfaces\ProjectMembersRepositoryInterface;

final readonly class ProjectMembersService implements ProjectMembersServiceInterface
{
    private ProjectMembersRepositoryInterface $projectMembersRepo;

    function __construct(ProjectMembersRepositoryInterface $projectMembersRepo)
    {
        $this->projectMembersRepo = $projectMembersRepo;
    }

    //region Member Retrieval
    public function getProjectMembersByProjectId(int $projectId): array
    {
        return $this->projectMembersRepo->findProjectMembersByProjectId($projectId);
    }

    public function getProjectInvitesByProjectId(int $projectId): array
    {
        return $this->projectMembersRepo->findProjectInvitesByProjectId($projectId);
    }
    //endregion


    //region Member Management
    public function promoteProjectMember(int $projectId, int $userId): void
    {
        $this->projectMembersRepo->promoteProjectMember($projectId, $userId);
    }

    public function demoteProjectMember(int $projectId, int $userId): void
    {
        $this->projectMembersRepo->demoteProjectMember($projectId, $userId);
    }

    public function removeProjectMember(int $projectId, int $userId): void
    {
        $this->projectMembersRepo->removeProjectMember($projectId, $userId);
    }

    //endregion


    //region Invite Codes
    public function generateProjectInviteCodes(int $projectId, DateTimeImmutable $expiresAt, int $count): void
    {
        $now = new DateTimeImmutable(); // Gets set in database to current time
        $createdBy = $_SESSION['auth']['username'];
        $invites = [];

        // Check if given date is in the past, and count is within allowed range
        if ($expiresAt <= $now)
            throw new ProjectMembersException(ProjectMembersException::INVITE_EXPIRATION_INVALID);
        if ($count <= 0 || $count > 10)
            throw new ProjectMembersException(ProjectMembersException::INVITE_COUNT_INVALID);

        // id is temporarily 0, automatically set by the database
        for ($i = 0; $i < $count; $i++)
            $invites[] = new ProjectInvite(0, $projectId, $this->generateInviteCode(
                16), $expiresAt, null, $createdBy, $now);

        $this->projectMembersRepo->createProjectInviteCodes($invites);
    }

    public function joinProjectByInviteCode(string $inviteCode): int
    {
        if (!isset($inviteCode))
            throw new ProjectMembersException(ProjectMembersException::INVITE_CODE_INVALID);

        // Try repository operations, catch their 'technical' exceptions
        // and re-throw them to user with additional context as 'business' exceptions
        try {
            return $this->projectMembersRepo->joinProjectByInviteCode($inviteCode, (int)$_SESSION['auth']['userId']);
        } catch (InviteNotFoundException) {
            throw new ProjectMembersException(ProjectMembersException::INVITE_CODE_INVALID);
        } catch (InviteCodeExpiredOrUsedException) {
            throw new ProjectMembersException(ProjectMembersException::INVITE_CODE_EXPIRED_OR_USED);
        }
    }

    public function deleteProjectInviteCode(int $projectId, int $inviteId): void
    {
        $success = $this->projectMembersRepo->deleteProjectInviteCode($projectId, $inviteId);
        if (!$success)
            throw new ProjectMembersException(ProjectMembersException::INVITE_REMOVAL_FAILED);
    }
    //endregion


    /** Generates a random invite code of specified length.
     * @return string of 16 random alphanumeric characters
     */
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