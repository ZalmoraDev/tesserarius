<?php

namespace App\Services;

use App\Models\Project;

interface ProjectMembersServiceInterface
{
    public function getProjectMembersByProjectId(int $projectId): ?array; // array of ProjectMemberDto
    public function getProjectInviteCodes(int $projectId): ?array; // array of ProjectInviteCodesDto

    public function generateProjectInviteCode(int $projectId): ?string;
    public function joinProjectByInviteCode(int $userId, string $inviteCode): bool;
    public function removeProjectMember(int $projectId, int $userId): bool;

}