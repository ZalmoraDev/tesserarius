<?php

namespace App\Services;

use App\Services\Exceptions\ServiceException;
use DateMalformedStringException;
use DateTimeImmutable;

use App\Models\ProjectInvite;
use App\Services\Exceptions\ProjectMembersException;
use App\Services\Interfaces\ProjectMembersServiceInterface;
use App\Repositories\Exceptions\ProjectMembersRepoException;
use App\Repositories\Interfaces\ProjectMembersRepositoryInterface;
use Exception;

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
        try {
            return $this->projectMembersRepo->findProjectMembersByProjectId($projectId);
        } catch (ProjectMembersRepoException $e) {
            error_log("Repository error in getProjectMembersByProjectId: " . $e->getMessage());
            throw new ProjectMembersException(ServiceException::DATABASE_ERROR);
        } catch (DateMalformedStringException $e) {
            error_log("Date format error in getProjectMembersByProjectId: " . $e->getMessage());
            throw new ProjectMembersException(ServiceException::INVALID_DATE_FORMAT);
        } catch (Exception $e) {
            error_log("Unexpected error in getProjectMembersByProjectId: " . $e->getMessage());
            throw new ProjectMembersException(ServiceException::UNEXPECTED_ERROR);
        }
    }

    public function getProjectInvitesByProjectId(int $projectId): array
    {
        try {
            return $this->projectMembersRepo->findProjectInvitesByProjectId($projectId);
        } catch (ProjectMembersRepoException $e) {
            error_log("Repository error in getProjectInvitesByProjectId: " . $e->getMessage());
            throw new ProjectMembersException(ServiceException::DATABASE_ERROR);
        } catch (DateMalformedStringException $e) {
            error_log("Date format error in getProjectInvitesByProjectId: " . $e->getMessage());
            throw new ProjectMembersException(ServiceException::INVALID_DATE_FORMAT);
        } catch (Exception $e) {
            error_log("Unexpected error in getProjectInvitesByProjectId: " . $e->getMessage());
            throw new ProjectMembersException(ServiceException::UNEXPECTED_ERROR);
        }
    }
    //endregion


    //region Member Management
    public function promoteProjectMember(int $projectId, int $userId): void
    {
        try {
            $this->projectMembersRepo->promoteProjectMember($projectId, $userId);
        } catch (ProjectMembersRepoException $e) {
            error_log("Repository error in promoteProjectMember: " . $e->getMessage());
            throw new ProjectMembersException(ServiceException::DATABASE_ERROR);
        } catch (Exception $e) {
            error_log("Unexpected error in promoteProjectMember: " . $e->getMessage());
            throw new ProjectMembersException(ServiceException::UNEXPECTED_ERROR);
        }
    }

    public function demoteProjectMember(int $projectId, int $userId): void
    {
        try {
            $this->projectMembersRepo->demoteProjectMember($projectId, $userId);
        } catch (ProjectMembersRepoException $e) {
            error_log("Repository error in demoteProjectMember: " . $e->getMessage());
            throw new ProjectMembersException(ServiceException::DATABASE_ERROR);
        } catch (Exception $e) {
            error_log("Unexpected error in demoteProjectMember: " . $e->getMessage());
            throw new ProjectMembersException(ServiceException::UNEXPECTED_ERROR);
        }
    }

    public function removeProjectMember(int $projectId, int $userId): void
    {
        try {
            $this->projectMembersRepo->removeProjectMember($projectId, $userId);
        } catch (ProjectMembersRepoException $e) {
            error_log("Repository error in removeProjectMember: " . $e->getMessage());
            throw new ProjectMembersException(ServiceException::DATABASE_ERROR);
        } catch (Exception $e) {
            error_log("Unexpected error in removeProjectMember: " . $e->getMessage());
            throw new ProjectMembersException(ServiceException::UNEXPECTED_ERROR);
        }
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

        try {
            $this->projectMembersRepo->createProjectInviteCodes($invites);
        } catch (ProjectMembersRepoException $e) {
            error_log("Repository error in generateProjectInviteCodes: " . $e->getMessage());
            throw new ProjectMembersException(ServiceException::DATABASE_ERROR);
        } catch (Exception $e) {
            error_log("Unexpected error in generateProjectInviteCodes: " . $e->getMessage());
            throw new ProjectMembersException(ServiceException::UNEXPECTED_ERROR);
        }
    }

    public function joinProjectByInviteCode(string $inviteCode): int
    {
        if (!isset($inviteCode))
            throw new ProjectMembersException(ProjectMembersException::INVITE_CODE_INVALID);

        // Try repository operations, catch their 'technical' exceptions
        // and re-throw them to user with additional context as 'business' exceptions
        try {
            return $this->projectMembersRepo->joinProjectByInviteCode($inviteCode, (int)$_SESSION['auth']['userId']);
        } catch (ProjectMembersRepoException $e) {
            // Map repository exceptions to service exceptions based on the error
            if ($e->getMessage() === ProjectMembersRepoException::INVITE_NOT_FOUND)
                throw new ProjectMembersException(ProjectMembersException::INVITE_CODE_INVALID);
            elseif ($e->getMessage() === ProjectMembersRepoException::INVITE_EXPIRED || $e->getMessage() === ProjectMembersRepoException::INVITE_ALREADY_USED)
                throw new ProjectMembersException(ProjectMembersException::INVITE_CODE_EXPIRED_OR_USED);
            elseif ($e->getMessage() === ProjectMembersRepoException::MEMBER_ALREADY_EXISTS)
                throw new ProjectMembersException(ProjectMembersException::INVITE_CODE_EXPIRED_OR_USED);

            // Generic repository error
            error_log("Repository error in joinProjectByInviteCode: " . $e->getMessage());
            throw new ProjectMembersException(ServiceException::DATABASE_ERROR);
        } catch (DateMalformedStringException $e) {
            error_log("Date format error in joinProjectByInviteCode: " . $e->getMessage());
            throw new ProjectMembersException(ServiceException::INVALID_DATE_FORMAT);
        } catch (Exception $e) {
            error_log("Unexpected error in joinProjectByInviteCode: " . $e->getMessage());
            throw new ProjectMembersException(ServiceException::UNEXPECTED_ERROR);
        }
    }

    public function deleteProjectInviteCode(int $projectId, int $inviteId): void
    {
        try {
            $success = $this->projectMembersRepo->deleteProjectInviteCode($projectId, $inviteId);
            if (!$success)
                throw new ProjectMembersException(ProjectMembersException::INVITE_REMOVAL_FAILED);
        } catch (ProjectMembersException $e) {
            // Re-throw business exceptions as-is
            throw $e;
        } catch (ProjectMembersRepoException $e) {
            error_log("Repository error in deleteProjectInviteCode: " . $e->getMessage());
            throw new ProjectMembersException(ServiceException::DATABASE_ERROR);
        } catch (Exception $e) {
            error_log("Unexpected error in deleteProjectInviteCode: " . $e->getMessage());
            throw new ProjectMembersException(ServiceException::UNEXPECTED_ERROR);
        }
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