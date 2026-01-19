<?php

namespace App\Services;

use App\Models\Project;
use DateTimeImmutable;

interface ProjectMembersServiceInterface
{
    public function getProjectMembersByProjectId(int $projectId): ?array; // array of ProjectMemberDto
    public function getProjectInviteCodes(int $projectId): ?array; // array of ProjectInviteCodesDto

    public function generateProjectInviteCodes(int $projectId, DateTimeImmutable $expiresAt, int $count): void;
    public function removeProjectInviteCode(int $inviteId): void;
    public function joinProjectByInviteCode(int $userId, string $inviteCode): bool;
    public function removeProjectMember(int $projectId, int $userId): bool;
}