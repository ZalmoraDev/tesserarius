<?php

namespace App\Controllers;

use DateTimeImmutable;
use App\Services\Exceptions\ServiceException;
use App\Services\ProjectMembersService;
use Exception;

/** Controller handling project member related actions
 * - POST: create & delete project
 * - POST: join-project
 * - POST: promote, demote & remove members */
final readonly class ProjectMembersController
{
    private ProjectMembersService $projectMemberService;

    public function __construct(ProjectMembersService $projectMembersService)
    {
        $this->projectMemberService = $projectMembersService;
    }


    //region POST Requests

    /** POST /project-members/create-invite/{$projectId}, handles the creation of a project invite */
    public function handleInviteCreation(int $projectId): void
    {
        try {
            $this->projectMemberService->generateProjectInviteCodes(
                $projectId,
                new DateTimeImmutable($_POST['expires_at']),
                $_POST['count']
            );
            $_SESSION['flash_successes'][] = 'Invite code(s) created successfully.';
        } catch (ServiceException $e) {
            $_SESSION['flash_errors'][] = $e->getMessage();
        } catch (Exception) {
            $_SESSION['flash_errors'][] = "An unexpected error occurred.";
        }
        $redirect = "/project/edit/" . $projectId;
        header("Location: $redirect", true, 302);
        exit;
    }

    /** POST /project-members/delete-invite/{$projectId}/{$inviteId}, handles the deletion of a project invite */
    public function handleInviteDeletion(int $projectId, int $inviteId): void
    {
        try {
            $this->projectMemberService->deleteProjectInviteCode($projectId, $inviteId);
            $_SESSION['flash_successes'][] = 'Invite deleted successfully.';
        } catch (ServiceException $e) {
            $_SESSION['flash_errors'][] = $e->getMessage();
        } catch (Exception) {
            $_SESSION['flash_errors'][] = "An unexpected error occurred.";
        }
        $redirect = "/project/edit/" . $projectId;
        header("Location: $redirect", true, 302);
        exit;
    }

    /** POST /project-members/join-project, handles joining a project by invite code */
    public function handleJoinByInviteCode(): void
    {
        try {
            $joinedProjectId = $this->projectMemberService->joinProjectByInviteCode($_POST['invite_code']);
            $_SESSION['flash_successes'][] = 'Successfully joined project.';
            $redirect = "/project/view/" . $joinedProjectId;
        } catch (ServiceException $e) {
            $_SESSION['flash_errors'][] = $e->getMessage();
            $redirect = "/";
        } catch (Exception) {
            $_SESSION['flash_errors'][] = "An unexpected error occurred.";
            $redirect = "/";
        }
        header("Location: $redirect", true, 302);
        exit;
    }

    /** POST /project-members/promote/{$projectId}/{$memberId}, handles promoting a project member.
     * Doesn't throw exceptions, as authorization for Owner is done by router.php. */
    public function handleMemberPromote(int $projectId, int $memberId): void
    {
        try {
            $this->projectMemberService->promoteProjectMember($projectId, $memberId);
            $_SESSION['flash_successes'][] = 'Member promoted successfully.';
        } catch (ServiceException $e) {
            $_SESSION['flash_errors'][] = $e->getMessage();
        } catch (Exception) {
            $_SESSION['flash_errors'][] = "An unexpected error occurred.";
        }
        $redirect = "/project/edit/" . $projectId;
        header("Location: $redirect", true, 302);
        exit;
    }

    /** POST /project-members/demote/{$projectId}/{$memberId}, handles demoting a project member.
     * Doesn't throw exceptions, as authorization for Owner is done by router.php. */
    public function handleMemberDemote(int $projectId, int $memberId): void
    {
        try {
            $this->projectMemberService->demoteProjectMember($projectId, $memberId);
            $_SESSION['flash_successes'][] = 'Member demoted successfully.';
        } catch (ServiceException $e) {
            $_SESSION['flash_errors'][] = $e->getMessage();
        } catch (Exception) {
            $_SESSION['flash_errors'][] = "An unexpected error occurred.";
        }
        $redirect = "/project/edit/" . $projectId;
        header("Location: $redirect", true, 302);
        exit;
    }

    /** POST /project-members/remove/{$projectId}/{$memberId}, handles removing a project member.
     * Doesn't throw exceptions, as authorization for Admin/Owner is done by router.php. */
    public function handleMemberRemoval(int $projectId, int $memberId): void
    {
        try {
            $this->projectMemberService->removeProjectMember($projectId, $memberId);
            $_SESSION['flash_successes'][] = 'Member removed successfully.';
        } catch (ServiceException $e) {
            $_SESSION['flash_errors'][] = $e->getMessage();
        } catch (Exception) {
            $_SESSION['flash_errors'][] = "An unexpected error occurred.";
        }
        $redirect = "/project/edit/" . $projectId;
        header("Location: $redirect", true, 302);
        exit;
    }
    //endregion
}