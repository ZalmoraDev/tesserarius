<?php

namespace App\Repositories\Interfaces;

use App\Dto\ProjectMemberDto;
use App\Models\Enums\UserRole;
use App\Models\ProjectInvite;
use App\Repositories\Exceptions\RepositoryException;

interface ProjectMembersRepositoryInterface
{
    //region Member Retrieval
    /** Fetches all project members by project ID.
     * @return ProjectMemberDto[]|null
     * */
    public function findProjectMembersByProjectId(int $projectId): ?array; // array of ProjectMemberDto

    /** Fetches all invite codes for a project by project ID.
     * @return ProjectInvite[]|null
     */
    public function findProjectInvitesByProjectId(int $projectId): ?array;
    //endregion


    //region Member Management
    /** Adds a user to a project with a specified role. (Member)*/
    public function addProjectMember(int $projectId, int $userId, UserRole $role): void;

    /** Promote 'Member' to 'Admin' in the project (Owner ONLY)*/
    public function promoteProjectMember(int $projectId, int $userId): void;

    /** Demote 'Admin' to 'Member' in the project (Owner ONLY)*/
    public function demoteProjectMember(int $projectId, int $userId): void;

    /** Remove user from the project (Admin / Owner ONLY)*/
    public function removeProjectMember(int $projectId, int $userId): void;
    //endregion


    //region Invite Codes
    /** Join a user to a project by invite code.
     * @return int ID of the project the user has joined.
     * @throws RepositoryException if invite code is invalid or used/expired.
     */
    public function joinProjectByInviteCode(string $inviteCode, int $userId): int;

    /** Uses Service made invite-code(s), which contains projectId, creator etc.
     * Used by repository to create entry/entries in DB
     * @return bool True on success
     */
    public function createProjectInviteCodes(array $invites): bool;

    /** Deletes a project invite code by its ID.
     * @return bool True if the invite was deleted, false otherwise.
     */
    public function deleteProjectInviteCode(int $projectId, int $inviteId): bool;
    //endregion
}