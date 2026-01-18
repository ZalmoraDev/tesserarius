<?php

namespace App\Controllers;

use App\Services\{Exceptions\ProjectException, ProjectMembersService, ProjectServiceInterface, TaskServiceInterface};

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
    public function handleInviteCreation(int $id) {
        try {
            $this->projectMemberService->generateProjectInviteCode(
                (int)$_POST['project_id'],
                new DateTimeImmutable($_POST['expires_at']),
                $_POST['count']
            );
            header("Location: /project/edit/" . $id, true, 302);
            exit;
        } catch (ProjectException $e) {
            $_SESSION['flash_errors'][] = $e->getMessage();
            header("Location: /project/create", true, 302);
            exit;
        }
    }

    /** POST /project-members/promote/{$projectId}/{$memberId}, handles the creation of a project invite */
    public function handleMemberPromote(int $id) {
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

    /** POST /project-members/demote/{$projectId}/{$memberId}, handles the creation of a project invite */
    public function handleMemberDemote(int $id) {
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
    public function handleUserRemoval(int $id) {
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