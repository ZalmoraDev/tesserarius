<?php

namespace App\Controllers;

use DateTimeImmutable;
use App\Services\{Exceptions\ProjectException,
    Exceptions\ProjectMembersException,
    ProjectMembersService,
    ProjectServiceInterface,
    TaskServiceInterface
};

final class ProjectMembersController
{
    private ProjectMembersService $projectMemberService;

    public function __construct(ProjectMembersService $projectMembersService)
    {
        $this->projectMemberService = $projectMembersService;
    }

    // -------------------- GET Requests --------------------

    // -------------------- POST Requests --------------------

    /** POST /project-members/create-invite/{$projectId}, handles the creation of a project invite */
    public function handleInviteCreation(int $projectId): void
    {
        try {
            $this->projectMemberService->generateProjectInviteCodes(
                $projectId,
                new DateTimeImmutable($_POST['expires_at']),
                $_POST['count']
            );
        } catch (ProjectMembersException $e) {
            $_SESSION['flash_errors'][] = $e->getMessage();
        }
        header("Location: /project/edit/" . $projectId, true, 302);
        exit;
    }

    /** POST /project-members/delete-invite/{$projectId}/{$inviteId}, handles the deletion of a project invite */
    public function handleInviteDeletion(int $projectId, int $inviteId): void
    {
        try {
            $this->projectMemberService->deleteProjectInviteCode($projectId, $inviteId);
        } catch (ProjectMembersException $e) {
            $_SESSION['flash_errors'][] = $e->getMessage();
        }
        header("Location: /project/edit/" . $projectId, true, 302);
        exit;
    }

    /** POST /project-members/join-project, handles joining a project by invite code */
    public function handleJoinByInviteCode(): void
    {
        try {
            $joinedProjectId = $this->projectMemberService->joinProjectByInviteCode($_POST['invite_code']);
            header("Location: /project/view/" . $joinedProjectId, true, 302);
            exit;
        } catch (ProjectMembersException $e) {
            $_SESSION['flash_errors'][] = $e->getMessage();
            header("Location: /", true, 302);
            exit;
        }
    }

    /** POST /project-members/promote/{$projectId}/{$memberId}, handles promoting a project member.
     * Doesn't throw exceptions, as authorization for Owner is done by router.php. */
    public function handleMemberPromote(int $projectId, int $memberId): void
    {
        $this->projectMemberService->promoteProjectMember($projectId, $memberId);
        header("Location: /project/edit/" . $projectId, true, 302);
        exit;
    }

    /** POST /project-members/demote/{$projectId}/{$memberId}, handles demoting a project member.
     * Doesn't throw exceptions, as authorization for Owner is done by router.php. */
    public function handleMemberDemote(int $projectId, int $memberId): void
    {
        $this->projectMemberService->demoteProjectMember($projectId, $memberId);
        header("Location: /project/edit/" . $projectId, true, 302);
        exit;
    }

    /** POST /project-members/remove/{$projectId}/{$memberId}, handles removing a project member.
     * Doesn't throw exceptions, as authorization for Admin/Owner is done by router.php. */
    public function handleMemberRemoval(int $projectId, int $memberId): void
    {
        $this->projectMemberService->removeProjectMember($projectId, $memberId);
        header("Location: /project/edit/" . $projectId, true, 302);
        exit;
    }
}