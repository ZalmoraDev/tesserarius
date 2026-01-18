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

    public function addProjectMember(int $projectId, int $userId, UserRole $role): void;
    public function createProjectInviteCodes(ProjectInvite $invite): bool;
    public function joinProjectByInviteCode(int $userId, string $inviteCode): bool;
    public function removeProjectMember(int $projectId, int $userId): bool;
}