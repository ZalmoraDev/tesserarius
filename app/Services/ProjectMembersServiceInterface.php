<?php

namespace App\Services;

use App\Models\Project;
use DateTimeImmutable;

interface ProjectMembersServiceInterface
{
    public function getProjectMembersByProjectId(int $projectId): array; // array of ProjectMemberDto
    public function getProjectInvitesByProjectId(int $projectId): array; // array of ProjectInviteCodesDto

    public function generateProjectInviteCodes(int $projectId, DateTimeImmutable $expiresAt, int $count): void;
    public function joinProjectByInviteCode(string $inviteCode): int;
    public function removeProjectInviteCode(int $inviteId): void;

    public function promoteProjectMember(int $projectId, int $userId): void;
    public function demoteProjectMember(int $projectId, int $userId): void;
    public function removeProjectMember(int $projectId, int $userId): void;
}