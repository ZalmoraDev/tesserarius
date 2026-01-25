<?php

namespace App\Services;

use App\Models\Enums\UserRole;
use App\Models\ProjectInvite;
use App\Repositories\Exceptions\ProjectMembers\InviteCodeExpiredOrUsedException;
use App\Repositories\Exceptions\ProjectMembers\InviteNotFoundException;
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

    /** Returns array of 'ProjectMemberDto's by $projectId */
    public function getProjectMembersByProjectId(int $projectId): array
    {
        return $this->projectMembersRepo->findProjectMembersByProjectId($projectId);
    }

    /** Returns array of 'ProjectInvite's by $projectId */
    public function getProjectInviteCodes(int $projectId): array
    {
        return $this->projectMembersRepo->findProjectInviteCodes($projectId);
    }


    // TODO: Add exceptions

    /** Generates one-or-more $projectInviteCode's for given $projectId,
     * with expiration date and total amount to generate. */
    public function generateProjectInviteCodes(int $projectId, DateTimeImmutable $expiresAt, int $count): void
    {
        $now = new DateTimeImmutable(); // Gets set in database to current time
        $createdBy = $_SESSION['auth']['username'];
        $invites = [];

        // id is temporarily 0, automatically set by the database.
        // Generates $count amount of invite codes to be created in the database
        for ($i = 0; $i < $count; $i++)
            $invites[] = new ProjectInvite(0, $projectId, $this->generateInviteCode(
                16), $expiresAt, null, $createdBy, $now);

        $this->projectMembersRepo->createProjectInviteCodes($invites);
        // TODO: Error handling
    }

    /** Removes a project invite code by its ID.
     * @throws ProjectMembersException if removal fails.
     */
    public function removeProjectInviteCode(int $inviteId): void
    {
        $success = $this->projectMembersRepo->removeProjectInviteCode($inviteId);
        if (!$success)
            throw new ProjectMembersException(ProjectMembersException::INVITE_REMOVAL_FAILED);
    }

    public function joinProjectByInviteCode(string $inviteCode): int
    {
        if (!isset($inviteCode))
            throw new ProjectMembersException(ProjectMembersException::INVITE_CODE_INVALID);

        // Try repository operations, catch their 'technical' exceptions
        // and re-throw them to user with additional context as 'business' exceptions
        try {
            return $this->projectMembersRepo->joinProjectByInviteCode($inviteCode, (int)$_SESSION['auth']['userId']);
        } catch (InviteNotFoundException $e) {
            throw new ProjectMembersException(ProjectMembersException::INVITE_CODE_INVALID);
        } catch (InviteCodeExpiredOrUsedException $e) {
            throw new ProjectMembersException(ProjectMembersException::INVITE_CODE_EXPIRED_OR_USED);
        }
    }

    public function removeProjectMember(int $projectId, int $userId): bool
    {
        // TODO: Implement removeProjectMember() method.
        return false;
    }

    /** Generates a random invite code of specified length. (always 16 characters) */
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