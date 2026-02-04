<?php

namespace App\Repositories\Interfaces;

use App\Dto\ProjectMemberDto;
use App\Models\Enums\UserRole;
use App\Models\ProjectInvite;
use App\Repositories\Exceptions\ProjectMembersRepoException;
use App\Repositories\Exceptions\RepositoryException;
use DateMalformedStringException;

interface ProjectMembersRepositoryInterface
{
    //region Member Retrieval
    /** Fetches all project members by project ID.
     * @return ProjectMemberDto[]|null
     * @throws DateMalformedStringException if database returns invalid date format
     * @throws ProjectMembersRepoException if database query fails
     * */
    public function findProjectMembersByProjectId(int $projectId): ?array; // array of ProjectMemberDto

    /** Fetches all invite codes for a project by project ID.
     * @return ProjectInvite[]|null
     * @throws DateMalformedStringException if database returns invalid date format
     * @throws ProjectMembersRepoException if database query fails
     */
    public function findProjectInvitesByProjectId(int $projectId): ?array;
    //endregion


    //region Member Management
    /** Adds a user to a project with a specified role. (Member)
     * @throws ProjectMembersRepoException if member already exists or database operation fails
     */
    public function addProjectMember(int $projectId, int $userId, UserRole $role): void;

    /** Promote 'Member' to 'Admin' in the project (Owner ONLY)
     * @throws ProjectMembersRepoException if member not found or database operation fails
     */
    public function promoteProjectMember(int $projectId, int $userId): void;

    /** Demote 'Admin' to 'Member' in the project (Owner ONLY)
     * @throws ProjectMembersRepoException if member not found or database operation fails
     */
    public function demoteProjectMember(int $projectId, int $userId): void;

    /** Remove user from the project (Admin / Owner ONLY)
     * @throws ProjectMembersRepoException if member not found or database operation fails
     */
    public function removeProjectMember(int $projectId, int $userId): void;
    //endregion


    //region Invite Codes
    /** Join a user to a project by invite code.
     * @return int ID of the project the user has joined.
     * @throws DateMalformedStringException if database returns invalid date format
     * @throws ProjectMembersRepoException if invite not found, expired, used, member already exists, or database operation fails
     */
    public function joinProjectByInviteCode(string $inviteCode, int $userId): int;

    /** Uses Service made invite-code(s), which contains projectId, creator etc.
     * Used by repository to create entry/entries in DB
     * @return bool True on success
     * @throws ProjectMembersRepoException if database operation fails
     */
    public function createProjectInviteCodes(array $invites): bool;

    /** Deletes a project invite code by its ID.
     * @throws ProjectMembersRepoException if invite not found or database operation fails
     */
    public function deleteProjectInviteCode(int $projectId, int $inviteId): void;
    //endregion
}