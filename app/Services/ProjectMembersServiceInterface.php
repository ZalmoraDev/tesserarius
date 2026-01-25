<?php

namespace App\Services;

use App\Models\Project;
use DateTimeImmutable;

interface ProjectMembersServiceInterface
{
    public function getProjectMembersByProjectId(int $projectId): array; // array of ProjectMemberDto
    public function getProjectInviteCodes(int $projectId): array; // array of ProjectInviteCodesDto

    public function generateProjectInviteCodes(int $projectId, DateTimeImmutable $expiresAt, int $count): void;
    public function joinProjectByInviteCode(string $inviteCode): int;
    public function removeProjectInviteCode(int $inviteId): void;

    public function removeProjectMember(int $projectId, int $userId): bool;
}