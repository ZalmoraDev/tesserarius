<?php

namespace App\Repositories;

use App\Dto\ProjectMemberDto;
use App\Models\Project;
use DateTimeImmutable;

interface ProjectMembersRepositoryInterface
{
    public function findProjectMembersByProjectId(int $projectId): ?array; // array of ProjectMemberDto
    public function findProjectInviteCodes(int $projectId): ?array;

    public function generateProjectInviteCode(int $projectId, DateTimeImmutable $expiresAt, int $count): ?string;
    public function joinProjectByInviteCode(int $userId, string $inviteCode): bool;
    public function removeProjectMember(int $projectId, int $userId): bool;
}