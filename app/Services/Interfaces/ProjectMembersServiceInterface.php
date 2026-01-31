<?php

namespace App\Services\Interfaces;

use App\Dto\ProjectMemberDto;
use App\Models\ProjectInvite;
use App\Services\Exceptions\ProjectMembersException;
use DateTimeImmutable;

interface ProjectMembersServiceInterface
{
    //region Member Retrieval
    /** Returns array of ProjectMemberDto by $projectId
     * @return ProjectMemberDto[]
     */
    public function getProjectMembersByProjectId(int $projectId): array; // array of ProjectMemberDto

    /** Returns array of ProjectInvite by $projectId
     * @return ProjectInvite[]
     */
    public function getProjectInvitesByProjectId(int $projectId): array; // array of ProjectInviteCodesDto
    //endregion


    //region Member Management
    /** Promote 'Member' to 'Admin' in the project (Owner ONLY)*/
    public function promoteProjectMember(int $projectId, int $userId): void;

    /** Demote 'Admin' to 'Member' in the project (Owner ONLY)*/
    public function demoteProjectMember(int $projectId, int $userId): void;

    /** Removes a user from the project members (Admin / Owner ONLY)*/
    public function removeProjectMember(int $projectId, int $userId): void;
    //endregion


    //region Invite Codes
    /** Generate one-or-more $projectInviteCode's for given $projectId,
     * with expiration date and total amount to generate.
     * @throws ProjectMembersException if generation fails.
     */
    public function generateProjectInviteCodes(int $projectId, DateTimeImmutable $expiresAt, int $count): void;

    /** Joins the project associated with the given invite code for the current user.
     * @return int ID of the project the user has joined.
     * @throws ProjectMembersException if invite code is invalid, expired, or already used.
     */
    public function joinProjectByInviteCode(string $inviteCode): int;

    /** Delete a project invite code by its ID.
     * @throws ProjectMembersException if removal fails.
     */
    public function deleteProjectInviteCode(int $projectId, int $inviteId): void;
    //endregion
}