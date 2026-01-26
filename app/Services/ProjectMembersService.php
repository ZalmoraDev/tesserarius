<?php

namespace App\Services;

use App\Dto\ProjectMemberDto;
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

    /** Returns array of ProjectMemberDto by $projectId
     * @return ProjectMemberDto[]
     */
    public function getProjectMembersByProjectId(int $projectId): array
    {
        return $this->projectMembersRepo->findProjectMembersByProjectId($projectId);
    }

    /** Returns array of ProjectInvite by $projectId
     * @return ProjectInvite[]
     */
    public function getProjectInvitesByProjectId(int $projectId): array
    {
        return $this->projectMembersRepo->findProjectInvitesByProjectId($projectId);
    }

    /** Generate one-or-more $projectInviteCode's for given $projectId,
     * with expiration date and total amount to generate.
     * @throws ProjectMembersException if generation fails.
     */
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

    /** Removes a project invite code by its ID.
     * @throws ProjectMembersException if removal fails.
     */
    public function removeProjectInviteCode(int $inviteId): void
    {
        $success = $this->projectMembersRepo->removeProjectInviteCode($inviteId);
        if (!$success)
            throw new ProjectMembersException(ProjectMembersException::INVITE_REMOVAL_FAILED);
    }

    /** Joins the project associated with the given invite code for the current user.
     * @return int ID of the project the user has joined.
     * @throws ProjectMembersException if the invite code is invalid, expired, or already used.
     */
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

    /** Promote 'Member' to 'Admin' in the project (Owner ONLY)*/
    public function promoteProjectMember(int $projectId, int $userId): void
    {
        $this->projectMembersRepo->promoteProjectMember($projectId, $userId);
    }

    /** Demote 'Admin' to 'Member' in the project (Owner ONLY)*/
    public function demoteProjectMember(int $projectId, int $userId): void
    {
        $this->projectMembersRepo->demoteProjectMember($projectId, $userId);
    }

    /** Removes a user from the project members (Admin / Owner ONLY)*/
    public function removeProjectMember(int $projectId, int $userId): void
    {
        $this->projectMembersRepo->removeProjectMember($projectId, $userId);
    }

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