<?php

namespace App\Controllers;

use DateTimeImmutable;
use App\Services\{Exceptions\ProjectException,
    Exceptions\ProjectMembersException,
    ProjectMembersService,
    ProjectServiceInterface,
    TaskServiceInterface};

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
    public function handleInviteCreation(int $projectId)
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

    /** POST /project-members/remove-invite/{$inviteId}, handles the creation of a project invite */
    public function handleInviteDeletion(int $inviteId)
    {
        try {
            $this->projectMemberService->removeProjectInviteCode($inviteId);
        } catch (ProjectMembersException $e) {
            $_SESSION['flash_errors'][] = $e->getMessage();
        }
        // Redirect by hidden input field project_id, will be replaced by JS Ajax later
        header("Location: /project/edit/" . $_POST['project_id'], true, 302);
        exit;
    }

    /** POST /project-members/promote/{$projectId}/{$memberId}, handles the creation of a project invite */
    public function handleMemberPromote(int $projectId)
    {
        try {
            $this->projectMemberService->addProjectMember(
                (int)$_POST['project_id'],
                (int)$_POST['user_id'],
                $_POST['role']
            );
            header("Location: /project/view/" . $projectId, true, 302);
            exit;
        } catch (ProjectException $e) {
            $_SESSION['flash_errors'][] = $e->getMessage();
            header("Location: /project/create", true, 302);
            exit;
        }
    }

    /** POST /project-members/demote/{$projectId}/{$memberId}, handles the creation of a project invite */
    public function handleMemberDemote(int $id)
    {
        try {
            $this->projectMemberService->addProjectMember(
                (int)$_POST['project_id'],
                (int)$_POST['user_id'],
                $_POST['role']
            );
            header("Location: /project/view/" . $id, true, 302);
            exit;
        } catch (ProjectException $e) {
            $_SESSION['flash_errors'][] = $e->getMessage();
            header("Location: /project/create", true, 302);
            exit;
        }
    }

    /** POST /project/create-invite, handles the creation of a project invite */
    public function handleUserRemoval(int $id)
    {
        try {
            $this->projectMemberService->addProjectMember(
                (int)$_POST['project_id'],
                (int)$_POST['user_id'],
                $_POST['role']
            );
            header("Location: /project/view/" . $id, true, 302);
            exit;
        } catch (ProjectException $e) {
            $_SESSION['flash_errors'][] = $e->getMessage();
            header("Location: /project/create", true, 302);
            exit;
        }
    }
}