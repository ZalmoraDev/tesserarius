<?php

namespace App\Repositories;

use App\Dto\ProjectMemberDto;
use App\Models\Project;

interface ProjectMembersRepositoryInterface
{
    public function findProjectMembersByProjectId(int $projectId): ?array; // array of ProjectMemberDto

    public function generateProjectInviteCode(int $projectId): ?string;
    public function joinProjectByInviteCode(int $userId, string $inviteCode): bool;
    public function removeProjectMember(int $projectId, int $userId): bool;
}