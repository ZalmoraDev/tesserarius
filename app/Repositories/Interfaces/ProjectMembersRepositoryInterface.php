<?php

namespace App\Repositories\Interfaces;

use App\Models\Enums\UserRole;

interface ProjectMembersRepositoryInterface
{
    public function findProjectMembersByProjectId(int $projectId): ?array; // array of ProjectMemberDto

    public function findProjectInvitesByProjectId(int $projectId): ?array;
    public function joinProjectByInviteCode(string $inviteCode, int $userId): int;
    public function createProjectInviteCodes(array $invites): bool;
    public function deleteProjectInviteCode(int $projectId, int $inviteId): bool;

    public function addProjectMember(int $projectId, int $userId, UserRole $role): void;
    public function promoteProjectMember(int $projectId, int $userId): void;
    public function demoteProjectMember(int $projectId, int $userId): void;
    public function removeProjectMember(int $projectId, int $userId): void;
}