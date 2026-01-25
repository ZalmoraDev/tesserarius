<?php

namespace App\Repositories;

use App\Dto\ProjectMemberDto;
use App\Models\Enums\UserRole;
use App\Models\Project;
use App\Models\ProjectInvite;
use DateTimeImmutable;

interface ProjectMembersRepositoryInterface
{
    public function findProjectMembersByProjectId(int $projectId): ?array; // array of ProjectMemberDto

    public function findProjectInviteCodes(int $projectId): ?array;
    public function joinProjectByInviteCode(string $inviteCode, int $userId): int;
    public function createProjectInviteCodes(array $invites): bool;
    public function removeProjectInviteCode(int $inviteId): bool;

    public function addProjectMember(int $projectId, int $userId, UserRole $role): void;
    public function removeProjectMember(int $projectId, int $userId): bool;
}